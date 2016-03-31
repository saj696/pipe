<?php

namespace App\Http\Controllers\Setup;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\PurchasesReturnRequest;
use App\Models\GeneralJournal;
use App\Models\Material;
use App\Models\PersonalAccount;
use App\Models\PurchasesReturn;
use App\Models\PurchasesReturnDetail;
use App\Models\RawStock;
use App\Models\Supplier;
use App\Models\WorkspaceLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class PurchasesReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $purchase_return = PurchasesReturn::orderBy('id', 'DESC')->with('purchasesReturnDetail')->paginate(Config::get('common.pagination'));
        return view('purchasesReturn.index', compact('purchase_return'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::where('status', 1)->lists('company_name', 'id');
        $rmaterials = Material::where('status', 1)->select('name', 'id', 'type')->get();
        $materials = [];
        foreach ($rmaterials as $material) {
            if ($material->type != 1)
                $materials[$material->id] = Config::get('common.material_type')[$material->type] . ' - ' . $material->name;
            else
                $materials[$material->id] = $material->name;
        }
        $raw_stock = RawStock::lists('quantity', 'material_id');
        return view('purchasesReturn.create', compact('suppliers', 'materials', 'raw_stock'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(PurchasesReturnRequest $request)
    {
        if (!$request->input('items')) {
            Session()->flash('error_message', 'Purchases Return has not been Completed');
            return redirect('purchases');
        }
        $inputs = $request->input();
        DB::beginTransaction();
        try {
            $user_id = Auth::user()->id;
            $workspace_id = Auth::user()->workspace_id;
            $balance_type = Config::get('common.balance_type_intermediate');
            $transaction_type = Config::get('common.transaction_type.purchase_return');
            $person_type_supplier = Config::get('common.person_type_supplier');
            $year = CommonHelper::get_current_financial_year();
            $time = time();
            $purchase = New PurchasesReturn();
            $purchase->supplier_id = $request->input('supplier_id');
            $purchase->purchase_return_date = $request->input('purchase_return_date');
            $purchase->transportation_cost = $request->input('transportation_cost');
            $purchase->total_amout = $request->input('total');
            $purchase->return_type = $request->input('return_type');
            $purchase->created_at = time();
            $purchase->created_by = $user_id;
            $purchase->save();
            $purchase_return_id = $purchase->id;
            foreach ($request->input('items') as $item) {
                //purchase Return details
                $item['purchases_return_id'] = $purchase_return_id;
                $item['status'] = 1;
                $item['created_at'] = time();
                $item['created_by'] = $user_id;
                PurchasesReturnDetail::create($item);
                //update stock info
                RawStock::where(['material_id' => $item['material_id'], 'year' => $year, 'stock_type' => $balance_type])->decrement('quantity', $item['quantity'], ['updated_at' => $time, 'updated_by' => $user_id]);
            }
            //general journal entry
            if ($inputs['return_type'] == 1)//For Cash
            {
                // Update Workspace Ledger
                $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                $workspace->balance += $inputs['total']; //add Cash
                $workspace->updated_by = $user_id;
                $workspace->updated_at = $time;
                $workspace->save();

                $workspace = WorkspaceLedger::where(['account_code' => 26000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                $workspace->balance += $inputs['total']; //Add Purchase Return
                $workspace->updated_by = $user_id;
                $workspace->updated_at = $time;
                $workspace->save();

                //Insert data into General Journal

                $journal = new GeneralJournal();
                $journal->date = strtotime($request->input('purchase_return_date'));
                $journal->transaction_type = $transaction_type;
                $journal->reference_id = $purchase_return_id;
                $journal->year = $year;
                $journal->account_code = 11000; //Cash
                $journal->workspace_id = $workspace_id;
                $journal->amount = $inputs['total'];
                $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                $journal->created_by = $user_id;
                $journal->created_at = $time;
                $journal->save();

                $journal = new GeneralJournal();
                $journal->date = strtotime($request->input('purchase_return_date'));
                $journal->transaction_type = $transaction_type;
                $journal->reference_id = $purchase_return_id;
                $journal->year = $year;
                $journal->account_code = 26000; //purchase Return
                $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                $journal->workspace_id = $workspace_id;
                $journal->amount = $inputs['total'];;
                $journal->created_by = $user_id;
                $journal->created_at = $time;
                $journal->save();

            } elseif ($inputs['return_type'] == 2) // For Pay due
            {
                // Update Workspace Ledger
                $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                $workspace->balance -= $inputs['total']; //Subtract Liabilities Account Payable
                $workspace->updated_by = $user_id;
                $workspace->updated_at = $time;
                $workspace->save();
                $workspace = WorkspaceLedger::where(['account_code' => 26000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                $workspace->balance += $inputs['total']; //Add Purchase Return
                $workspace->updated_by = $user_id;
                $workspace->updated_at = $time;
                $workspace->save();

                //Insert data into General Journal
                $journal = new GeneralJournal();
                $journal->date = strtotime($request->input('purchase_return_date'));
                $journal->transaction_type = $transaction_type;
                $journal->reference_id = $purchase_return_id;
                $journal->year = $year;
                $journal->account_code = 41000; // Liabilities Account Payable
                $journal->workspace_id = $workspace_id;
                $journal->amount = $inputs['total'];
                $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                $journal->created_by = $user_id;
                $journal->created_at = $time;
                $journal->save();

                $journal = new GeneralJournal();
                $journal->date = strtotime($request->input('purchase_return_date'));
                $journal->transaction_type = $transaction_type;
                $journal->reference_id = $purchase_return_id;
                $journal->year = $year;
                $journal->account_code = 26000; //Product Sales Return
                $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                $journal->workspace_id = $workspace_id;
                $journal->amount = $inputs['total'];;
                $journal->created_by = $user_id;
                $journal->created_at = $time;
                $journal->save();

                // Update Personal Account
                $personal = PersonalAccount::where('person_id', $inputs['supplier_id'])->where('person_type', $person_type_supplier)->first();
                $personal->balance -= $inputs['total'];
                $personal->updated_by = $user_id;
                $personal->updated_at = $time;
                $personal->save();

            } elseif ($inputs['return_type'] == 3)//For Due
            {
                // Update Workspace Ledger
                $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                $workspace->balance += $inputs['total']; //Account Receiveable
                $workspace->updated_by = $user_id;
                $workspace->updated_at = $time;
                $workspace->save();
                $workspace = WorkspaceLedger::where(['account_code' => 26000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                $workspace->balance += $inputs['total']; //Add Purchase Return
                $workspace->updated_by = $user_id;
                $workspace->updated_at = $time;
                $workspace->save();

                //Insert data into General Journal

                $journal = new GeneralJournal();
                $journal->date = strtotime($request->input('purchase_return_date'));
                $journal->transaction_type = $transaction_type;
                $journal->reference_id = $purchase_return_id;
                $journal->year = $year;
                $journal->account_code = 12000; //Account Receiveable
                $journal->workspace_id = $workspace_id;
                $journal->amount = $inputs['total'];
                $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                $journal->created_by = $user_id;
                $journal->created_at = $time;
                $journal->save();

                $journal = new GeneralJournal();
                $journal->date = strtotime($request->input('purchase_return_date'));
                $journal->transaction_type = $transaction_type;
                $journal->reference_id = $purchase_return_id;
                $journal->year = $year;
                $journal->account_code = 26000;  //Add Purchase Return
                $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                $journal->workspace_id = $workspace_id;
                $journal->amount = $inputs['total'];;
                $journal->created_by = $user_id;
                $journal->created_at = $time;
                $journal->save();

                //Update Personal Account
                $personal = PersonalAccount::where('person_id', $inputs['customer_id'])->where('person_type', $inputs['customer_type'])->first();
                $personal->due += $inputs['total'];
                $personal->updated_by = $user_id;
                $personal->updated_at = $time;
                $personal->save();
            } elseif ($inputs['return_type'] == 4)  //For Pay Due & Cash Return
            {
                // Update Workspace Ledger
                $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                $workspace->balance += $inputs['cash_return_amount'];
                $workspace->updated_by = $user_id;
                $workspace->updated_at = $time;
                $workspace->save();
                $workspace = WorkspaceLedger::where(['account_code' => 26000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                $workspace->balance += $inputs['total'];
                $workspace->updated_by = $user_id;
                $workspace->updated_at = $time;
                $workspace->save();

                $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                $workspace->balance -= $inputs['pay_due_amount']; //Account Payable
                $workspace->updated_by = $user_id;
                $workspace->updated_at = $time;
                $workspace->save();

                //Insert data into General Journal
                $journal = new GeneralJournal();
                $journal->date = strtotime($request->input('purchase_return_date'));
                $journal->transaction_type = $transaction_type;
                $journal->reference_id = $purchase_return_id;
                $journal->year = $year;
                $journal->account_code = 11000;      //Cash
                $journal->workspace_id = $workspace_id;
                $journal->amount = $inputs['cash_return_amount'];
                $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                $journal->created_by = $user_id;
                $journal->created_at = $time;
                $journal->save();

                $journal = new GeneralJournal();
                $journal->date = strtotime($request->input('purchase_return_date'));
                $journal->transaction_type = $transaction_type;
                $journal->reference_id = $purchase_return_id;
                $journal->year = $year;
                $journal->account_code = 26000;      //Product Sales Return
                $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                $journal->workspace_id = $workspace_id;
                $journal->amount = $inputs['total'];
                $journal->created_by = $user_id;
                $journal->created_at = $time;
                $journal->save();

                $journal = new GeneralJournal();
                $journal->date = strtotime($request->input('purchase_return_date'));
                $journal->transaction_type = $transaction_type;
                $journal->reference_id = $purchase_return_id;
                $journal->year = $year;
                $journal->account_code = 41000;   // Account Payable
                $journal->workspace_id = $workspace_id;
                $journal->amount = $inputs['pay_due_amount'];
                $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                $journal->created_by = $user_id;
                $journal->created_at = $time;
                $journal->save();

                //Update Personal Account
                $personal = PersonalAccount::where('person_id', $inputs['customer_id'])->where('person_type', $inputs['customer_type'])->first();
                $personal->blance -= $inputs['pay_due_amount'];
                $personal->updated_by = $user_id;
                $personal->updated_at = $time;
                $personal->save();
            }
            DB::commit();
            Session()->flash('flash_message', 'Purchases Return has been Completed');
        } catch (\Exception $e) {
            DB::rollBack();
            Session()->flash('flash_error', 'Purchases Return has not Completed!');
        }
        return redirect('purchases_return');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
