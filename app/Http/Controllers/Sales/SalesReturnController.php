<?php

namespace App\Http\Controllers\Sales;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Customer;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\Stock;
use App\Models\WorkspaceLedger;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;

class SalesReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }


    public function index()
    {
        $salesReturns = DB::table('sales_return')->select('sales_return.*', 'sales.*')->join('sales_return_details as sales', 'sales_return.id', '=', 'sales.sales_return_id')->get();
        return view('sales.salesReturn.index')->with(compact('salesReturns'));

    }


    public function create()
    {

        $customers = Customer::where('status', 1)->lists('name', 'id');
        return view('sales.salesReturn.create')->with('customers', $customers);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required',
            'product' => 'required|array',
            'total' => 'required|numeric',
            'return_type' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $inputs = $request->input();
                $user_id = Auth::user()->id;
                $workspace_id = Auth::user()->workspace_id;
                $balance_type = Config::get('common.balance_type_intermediate');
                $transaction_type = Config::get('common.transaction_type.sales_return');
                $time = time();
                $date = strtotime(date('d-m-Y'));
                $year = CommonHelper::get_current_financial_year();
                $data['customer_id'] = $inputs['customer_id'];
                $data['workspace_id'] = $workspace_id;
                $data['customer_type'] = $inputs['customer_type'];
                $data['total_amount'] = $inputs['total'];
                if ($inputs['return_type'] == 3) {
                    $data['due'] = $inputs['total'];;
                } elseif ($inputs['return_type'] == 4) {
                    $data['due'] = $inputs['total'] - $inputs['due_paid'];
                }
                if (isset($inputs['due_paid'])) {
                    $data['due_paid'] = $inputs['due_paid'];
                }
                $data['return_type'] = $inputs['return_type'];
                $data['date'] = $time;
                $data['created_by'] = $user_id;
                $data['created_at'] = $time;
                $sales_return_id = DB::table('sales_return')->insertGetId($data);
                unset($data);

                $data['sales_return_id'] = $sales_return_id;
                $data['created_by'] = $user_id;
                $data['created_at'] = $time;

                foreach ($inputs['product'] as $product) {
                    $data['product_id'] = $product['product_id'];
                    $data['quantity'] = $product['quantity_returned'];
                    $data['unit_price'] = $product['unit_price'];
                    $data['unit_type'] = $product['unit_type'];
                    DB::table('sales_return_details')->insert($data);

                    $quantity_returned = $product['quantity_returned'];

                    $stock = Stock::where('year','=',$year)->where('stock_type','=',$balance_type)->where('product_id','=',$product['product_id'])->first();
                    if ($product['unit_type'] == 2) {
                        $quantity_returned = ($product['quantity_returned'] / $product['weight']) * $product['length'];
                    }
                    $stock->quantity += $quantity_returned;
                    $stock->updated_by = $user_id;
                    $stock->updated_at = $time;
                    $stock->update();
                }


                if ($inputs['return_type'] == 1) {                  //For Cash

                    // Update Workspace Ledger
                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    if ($workspace->balance < $inputs['total']) {
                        Session()->flash('warning_message', 'Insufficient cash balance!');
                        throw new \Exception();
                    }
                    $workspace->balance -= $inputs['total']; //Subtract Cash
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 32000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $inputs['total']; //Add Product Sales Return
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    //Insert data into General Journal

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = $year;
                    $journal->account_code = 11000; //Cash
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['total'];
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = $year;
                    $journal->account_code = 32000; //Product Sales Return
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['total'];;
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                } elseif ($inputs['return_type'] == 2) {   // For Pay due


                    // Update Workspace Ledger
                    $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= $inputs['total']; //Subtract Account Receivable
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 32000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $inputs['total']; //Add Product Sales Return
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    //Insert data into General Journal

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = $year;
                    $journal->account_code = 12000; //Account Receivable
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['total'];
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = $year;
                    $journal->account_code = 32000; //Product Sales Return
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['total'];;
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    // Update Personal Account
                    $personal = PersonalAccount::where('person_id', $inputs['customer_id'])->where('person_type', $inputs['customer_type'])->first();
                    $personal->due -= $inputs['due_paid'];
                    if ($inputs['total'] > $inputs['due_paid']) {
                        $personal->balance += ($inputs['total'] - $inputs['due_paid']);
                    }
                    $personal->updated_by = $user_id;
                    $personal->updated_at = $time;
                    $personal->save();

                } elseif ($inputs['return_type'] == 3) {   //For Due

                    // Update Workspace Ledger
                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $inputs['total']; //Add Account Payable
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                    $workspace = WorkspaceLedger::where(['account_code' => 32000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $inputs['total']; //Add Product Sales Return
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    //Insert data into General Journal

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = $year;
                    $journal->account_code = 41000; //Account Payable
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['total'];
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = $year;
                    $journal->account_code = 32000;  //Product Sales Return
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['total'];;
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    //Update Personal Account
                    $personal = PersonalAccount::where('person_id', $inputs['customer_id'])->where('person_type', $inputs['customer_type'])->first();
                    $personal->balance += $inputs['total'];
                    $personal->updated_by = $user_id;
                    $personal->updated_at = $time;
                    $personal->save();
                } elseif ($inputs['return_type'] == 4) { //For Pay Due & Cash Return

                    // Update Workspace Ledger
                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    if ($workspace->balance < ($inputs['total'] - $inputs['due_paid'])) {
                        Session()->flash('warning_message', 'Insufficient cash balance!');
                        throw new \Exception();
                    }
                    $workspace->balance -= ($inputs['total'] - $inputs['due_paid']); //Subtract Cash
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                    $workspace = WorkspaceLedger::where(['account_code' => 32000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $inputs['total']; //Add Product Sales Return
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= $inputs['due_paid']; //Subtract Account Receivable
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    //Insert data into General Journal

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = $year;
                    $journal->account_code = 11000;      //Cash
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = ($inputs['total'] - $inputs['due_paid']);
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = $year;
                    $journal->account_code = 32000;      //Product Sales Return
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['total'];
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = $year;
                    $journal->account_code = 12000;   // Account Receivable
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['due_paid'];
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    //Update Personal Account

                    $personal = PersonalAccount::where('person_id', $inputs['customer_id'])->where('person_type', $inputs['customer_type'])->first();
                    $personal->due -= $inputs['due_paid'];
                    $personal->updated_by = $user_id;
                    $personal->updated_at = $time;
                    $personal->save();
                }

            });

        } catch (\Exception $e) {
            Session()->flash('error_message', 'Sales Returned Failed.');
            return Redirect::back();
        }

        Session()->flash('flash_message', 'Sales Returned Successful.');
        return redirect('sales_return');

    }
}
