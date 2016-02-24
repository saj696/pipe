<?php

namespace App\Http\Controllers\Sales;

use App\Models\Customer;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\Stock;
use App\Models\WorkspaceLedger;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use DB;

class SalesReturnController extends Controller
{
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
                $data['customer_id'] = $inputs['customer_id'];
                $data['customer_type'] = $inputs['customer_type'];
                $data['total_amount'] = $inputs['total'];
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
                    DB::table('sales_return_details')->insert($data);

                    $stock = Stock::find($product['product_id']);
                    $stock->quantity += $product['quantity_returned'];
                    $stock->updated_by = $user_id;
                    $stock->updated_at = $time;
                    $stock->save();
                }

                if ($inputs['return_type'] == 1) {                  //For Cash

                    // Update Workspace Ledger
                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type])->first();
                    $workspace->year = date('Y');
                    $workspace->balance -= $inputs['total']; //Subtract Cash
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 32000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type])->first();
                    $workspace->year = date('Y');
                    $workspace->balance += $inputs['total']; //Add Product Sales Return
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    /* // Update General Ledger
                     $general = GeneralLedger::where(['account_code' => 11000, 'balance_type' => $balance_type])->first();
                     $general->year = date('Y');
                     $general->balance -= $inputs['total']; //Subtract Cash
                     $general->updated_by = $user_id;
                     $general->updated_at = $time;
                     $general->save();

                     $general = GeneralLedger::where(['account_code' => 32000, 'balance_type' => $balance_type])->first();
                     $general->year = date('Y');
                     $general->balance += $inputs['total']; //Add Product Sales Return
                     $general->updated_by = $user_id;
                     $general->updated_at = $time;
                     $general->save();*/

                    //Insert data into General Journal

                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = date('Y');
                    $journal->account_code = 11000; //Cash
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['total'];
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = date('Y');
                    $journal->account_code = 32000; //Product Sales Return
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['total'];;
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                } elseif ($inputs['return_type'] == 2) {   // For Pay due


                    // Update Workspace Ledger
                    $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type])->first();
                    $workspace->year = date('Y');
                    $workspace->balance -= $inputs['total']; //Subtract Account Receivable
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                    $workspace = WorkspaceLedger::where(['account_code' => 32000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type])->first();
                    $workspace->year = date('Y');
                    $workspace->balance += $inputs['total']; //Add Product Sales Return
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    /*// Update General Ledger
                    $general = GeneralLedger::where(['account_code' => 12000, 'balance_type' => $balance_type])->first();
                    $general->year = date('Y');
                    $general->balance -= $inputs['total']; //Subtract Account Receivable
                    $general->updated_by = $user_id;
                    $general->updated_at = $time;
                    $general->save();

                    $general = GeneralLedger::where(['account_code' => 32000, 'balance_type' => $balance_type])->first();
                    $general->year = date('Y');
                    $general->balance += $inputs['total']; //Add Product Sales Return
                    $general->updated_by = $user_id;
                    $general->updated_at = $time;
                    $general->save();*/

                    //Insert data into General Journal

                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = date('Y');
                    $journal->account_code = 12000; //Account Receivable
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['total'];
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = date('Y');
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
                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type])->first();
                    $workspace->year = date('Y');
                    $workspace->balance += $inputs['total']; //Add Account Payable
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                    $workspace = WorkspaceLedger::where(['account_code' => 32000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type])->first();
                    $workspace->year = date('Y');
                    $workspace->balance += $inputs['total']; //Add Product Sales Return
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    /*// Update General Ledger
                    $general = GeneralLedger::where(['account_code' => 41000, 'balance_type' => $balance_type])->first();
                    $general->year = date('Y');
                    $general->balance += $inputs['total']; //Add Account Payable
                    $general->updated_by = $user_id;
                    $general->updated_at = $time;
                    $general->save();

                    $general = GeneralLedger::where(['account_code' => 32000, 'balance_type' => $balance_type])->first();
                    $general->year = date('Y');
                    $general->balance += $inputs['total']; //Add Product Sales Return
                    $general->updated_by = $user_id;
                    $general->updated_at = $time;
                    $general->save();*/

                    //Insert data into General Journal

                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = date('Y');
                    $journal->account_code = 41000; //Account Payable
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['total'];
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = date('Y');
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
                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type])->first();
                    $workspace->year = date('Y');
                    $workspace->balance -= ($inputs['total'] - $inputs['due_paid']); //Subtract Cash
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                    $workspace = WorkspaceLedger::where(['account_code' => 32000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type])->first();
                    $workspace->year = date('Y');
                    $workspace->balance += $inputs['total']; //Add Product Sales Return
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type])->first();
                    $workspace->year = date('Y');
                    $workspace->balance -= $inputs['due_paid']; //Subtract Account Receivable
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    /*// Update General Ledger
                    $general = GeneralLedger::where(['account_code' => 11000, 'balance_type' => $balance_type])->first();
                    $general->year = date('Y');
                    $general->balance -= ($inputs['total'] - $inputs['due_paid']); //Subtract Cash
                    $general->updated_by = $user_id;
                    $general->updated_at = $time;
                    $general->save();

                    $general = GeneralLedger::where(['account_code' => 32000, 'balance_type' => $balance_type])->first();
                    $general->year = date('Y');
                    $general->balance += ($inputs['total'] - $inputs['due_paid']); //Add Product Sales Return
                    $general->updated_by = $user_id;
                    $general->updated_at = $time;
                    $general->save();

                    $general = GeneralLedger::where(['account_code' => 12000, 'balance_type' => $balance_type])->first();
                    $general->year = date('Y');
                    $general->balance -= $inputs['due_paid']; //Subtract Cash
                    $general->updated_by = $user_id;
                    $general->updated_at = $time;
                    $general->save();*/

                    //Insert data into General Journal

                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = date('Y');
                    $journal->account_code = 11000;      //Cash
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = ($inputs['total'] - $inputs['due_paid']);
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = date('Y');
                    $journal->account_code = 32000;      //Product Sales Return
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['total'];
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_return_id;
                    $journal->year = date('Y');
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

            DB::rollback();
            Session()->flash('flash_message', 'Sales Returned Failed.');
            return Redirect::back();
        }

        DB::commit();
        Session()->flash('flash_message', 'Sales Returned Successful.');
        return redirect('sales_return');

    }
}
