<?php

namespace App\Http\Controllers\Setup;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\PurchaseRequest;
use App\Models\GeneralJournal;
use App\Models\Material;
use App\Models\PersonalAccount;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\RawStock;
use App\Models\Supplier;
use App\Models\WorkspaceLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PurchasesController extends Controller
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
        $purchases = Purchase::orderBy('id', 'DESC')->with('supplier', 'purchaseDetails')->paginate(Config::get('common.pagination'));
        return view('purchases.index', compact('purchases'));
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
        return view('purchases.create', compact('suppliers', 'materials'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(PurchaseRequest $request)
    {
//        $input = $request->input();
//        dd($input);
        if (!$request->input('items')) {
            Session()->flash('error_message', 'Purchases has not been Completed');
            return redirect('purchases');
        }
        try {
            DB::transaction(function () use ($request) {
                $user_id = Auth::user()->id;
                $workspace_id = Auth::user()->workspace_id;
                $year = CommonHelper::get_current_financial_year();

                $balance_type = Config::get('common.balance_type_intermediate');
                $transaction_type = Config::get('common.transaction_type.purchase');
                $person_type_supplier = Config::get('common.person_type_supplier');

                $time = time();
                $purchase = New Purchase();
                $purchase->supplier_id = $request->input('supplier_id');
                $purchase->purchase_date = $request->input('purchase_date');
                $purchase->transportation_cost = $request->input('transportation_cost');
                $purchase->paid = $request->input('paid');
                $purchase->total = $request->input('total');
                $purchase->created_at = time();
                $purchase->created_by = $user_id;
                $purchase->save();
                $purchase_id = $purchase->id;
                foreach ($request->input('items') as $item) {
                    //purchase details
                    $item['purchase_id'] = $purchase_id;
                    $item['status'] = 1;
                    $item['created_at'] = time();
                    $item['created_by'] = $user_id;
                    PurchaseDetail::create($item);
                    //update stock info
                    RawStock::where(['material_id' => $item['material_id'], 'year' => $year, 'stock_type' => $balance_type])->increment('quantity', $item['received_quantity'], ['updated_at' => $time, 'updated_by' => $user_id]);
                }
                // Account management
                $input = $request->input();
                if ($input['paid'])//check the cash amount
                {
                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    if ($input['paid'] > $workspace->balance)//if paid amount is greater than balance
                    {
                        Session()->flash('warning_message', 'Low Balance!! Purchase paid amount(' . $input['paid'] . ') is greater than Balance (' . $workspace->balance . ')');
                        throw new \Exception('error');
                    }
                }
                if ($input['paid'] == $input['total'])//if due is 0
                {
                    // Update Workspace Ledger
                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= $input['total']; //add Cash
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 25000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $input['total']; //Add Raw Purchase
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    //Insert data into General Journal
                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $purchase_id;
                    $journal->year = $year;
                    $journal->account_code = 11000; //Cash
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $input['total'];
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $purchase_id;
                    $journal->year = $year;
                    $journal->account_code = 25000; //purchase Return
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $input['total'];;
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();
                } elseif (!$input['paid'])//if paid is 0
                {
                    // Update Workspace Ledger
                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $input['total']; //add Cash
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 25000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $input['total']; //Add Raw Purchase
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    //Insert data into General Journal
                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $purchase_id;
                    $journal->year = $year;
                    $journal->account_code = 41000; //Cash
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $input['total'];
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $purchase_id;
                    $journal->year = $year;
                    $journal->account_code = 25000; //purchase Return
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $input['total'];;
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    // Update Personal Account
                    $personal = PersonalAccount::where('person_id', $input['supplier_id'])->where('person_type', $person_type_supplier)->first();
                    $personal->balance += $input['total'];
                    $personal->updated_by = $user_id;
                    $personal->updated_at = $time;
                    $personal->save();
                } elseif ($input['paid'] && $input['total'])// if some amount paid and some due
                {
                    $due_amount = $input['total'] - $input['paid'];
                    // Update Workspace Ledger
                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= $input['paid']; //sub Cash
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $due_amount; //add account payable credit
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 25000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $input['total']; //Add Raw Purchase
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    //Insert data into General Journal
                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $purchase_id;
                    $journal->year = $year;
                    $journal->account_code = 11000; //Cash
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $input['paid'];
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $purchase_id;
                    $journal->year = $year;
                    $journal->account_code = 25000; //Cash
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $input['total'];
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $purchase_id;
                    $journal->year = $year;
                    $journal->account_code = 41000; //Account Payable
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $due_amount;;
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();
                    // Update Personal Account
                    $personal = PersonalAccount::where('person_id', $input['supplier_id'])->where('person_type', $person_type_supplier)->first();

                    $personal->balance += $due_amount;
                    $personal->updated_by = $user_id;
                    $personal->updated_at = $time;
                    $personal->save();
                }
            });
        } catch (\Exception $e) {
//            dd($e);
            Session()->flash('error_message', 'Purchases has not been Completed');
            return Redirect::back();
        }
        Session()->flash('flash_message', 'Purchases has been Completed');
        return redirect('purchases');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $purchase = Purchase::with('purchaseDetails.material', 'supplier')->findOrFail($id);
//        dd($purchase);
        return view('purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $suppliers = Supplier::where('status', 1)->lists('company_name', 'id');
        $materials = Material::where('status', 1)->lists('name', 'id');
        $purchase = Purchase::with('purchaseDetails')->findOrFail($id);

        return view('purchases.edit', compact('suppliers', 'materials', 'purchase'));
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
        if (!$request->input('items')) {
            Session()->flash('error_message', 'Purchases Update has not been Completed');
            return redirect('purchases');
        }
        try {
            DB::transaction(function () use ($request, $id) {
                $time = time();
                $workspace_id = Auth::user()->workspace_id;
                $balance_type = Config::get('common.balance_type_intermediate');
                $transaction_type = Config::get('common.transaction_type.purchase');
                $person_type_supplier = Config::get('common.person_type_supplier');
                $year = CommonHelper::get_current_financial_year();
                $user_id = Auth::user()->id;

                $old_main_purchase = Purchase::findOrFail($id);
                $purchase = Purchase::findOrFail($id);
                $purchase->supplier_id = $request->input('supplier_id');
                $purchase->purchase_date = $request->input('purchase_date');
                $purchase->transportation_cost = $request->input('transportation_cost');
                $purchase->paid = $request->input('paid');
                $purchase->total = $request->input('total');
                $purchase->updated_at = time();
                $purchase->updated_by = $user_id;
                $purchase->update();
                //get all old items
                $arrange_old_items = [];
                $old_purchases = PurchaseDetail::where('purchase_id', $id)->get();
                foreach ($old_purchases as $old_purchase) {
                    $arrange_old_items[$old_purchase['material_id']] = $old_purchase;
                }
                foreach ($request->input('items') as $item) {
                    if (isset($arrange_old_items[$item['material_id']]))//if old data
                    {
                        // update old data
                        $PurchaseDetail = PurchaseDetail::findOrFail($arrange_old_items[$item['material_id']]['id']);
                        $PurchaseDetail->quantity = $item['quantity'];
                        $PurchaseDetail->received_quantity = $item['received_quantity'];
                        $PurchaseDetail->unit_price = $item['unit_price'];
                        $PurchaseDetail->status = 1;
                        $PurchaseDetail->updated_at = time();
                        $PurchaseDetail->updated_by = $user_id;
                        $PurchaseDetail->update();
                        //update stock info
                        if ($arrange_old_items[$item['material_id']]['received_quantity'] < $item['received_quantity']) {
                            $add_amount = $item['received_quantity'] - $arrange_old_items[$item['material_id']]['received_quantity'];
                            RawStock::where(['material_id' => $item['material_id'], 'year' => $year, 'stock_type' => $balance_type])
                                ->increment('quantity', $add_amount, ['updated_at' => $time, 'updated_by' => $user_id]);
                        } elseif ($arrange_old_items[$item['material_id']]['received_quantity'] > $item['received_quantity']) {
                            $sub_amount = $arrange_old_items[$item['material_id']]['received_quantity'] - $item['received_quantity'];
                            RawStock::where(['material_id' => $item['material_id'], 'year' => $year, 'stock_type' => $balance_type])
                                ->decrement('quantity', $sub_amount, ['updated_at' => $time, 'updated_by' => $user_id]);
                        }
                        unset($arrange_old_items[$item['material_id']]);
                    } else//if new data
                    {
                        //purchase details
                        $item['purchase_id'] = $id;
                        $item['status'] = 1;
                        $item['created_at'] = time();
                        $item['created_by'] = $user_id;
                        PurchaseDetail::create($item);
                        //update stock info
                        RawStock::where(['material_id' => $item['material_id'], 'year' => $year, 'stock_type' => $balance_type])
                            ->increment('quantity', $item['received_quantity'], ['updated_at' => $time, 'updated_by' => $user_id]);
                    }
                }
                //delete old data
                foreach ($arrange_old_items as $old_item) {
                    //reduce the stock info
                    RawStock::where(['material_id' => $item['material_id'], 'year' => $year, 'stock_type' => $balance_type])
                        ->decrement('quantity', $old_item['received_quantity'], ['updated_at' => $time, 'updated_by' => $user_id]);
                    //update the purchase info
                    $PurchaseDetail = PurchaseDetail::findOrFail($old_item['id']);
                    $PurchaseDetail->delete();
                }
                /*
                 * Account management
                 *
                 */
                $input = $request->input();
                if ($input['paid'] && $old_main_purchase['paid'] < $input['paid'])//check the cash amount
                {
                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $new_remain_paid_amount = $input['paid'] - $old_main_purchase['paid'];
                    if ($new_remain_paid_amount > $workspace->balance)//if paid amount is greater than balance
                    {
                        Session()->flash('warning_message', 'Low Balance!! New Purchase paid amount(' . $new_remain_paid_amount . ') is greater than Balance (' . $workspace->balance . ')');
                        throw new \Exception('error');
                    }
                }
                //update the accounting tables
                $workspace_id = Auth::user()->workspace_id;
                $balance_type = Config::get('common.balance_type_intermediate');
                $transaction_type = Config::get('common.transaction_type.purchase');
                $person_type_supplier = Config::get('common.person_type_supplier');
                $user_id = Auth::user()->id;
                $time = time();

                //update general journal for raw material purchase
                $general_journal = GeneralJournal::where([
                    'transaction_type' => $transaction_type,
                    'reference_id' => $id,
                    'account_code' => 25000,
                    'year' => $year,
                    'workspace_id' => $workspace_id
                ])->first();
                $general_journal->amount = $input['total'];
                $general_journal->updated_by = $user_id;
                $general_journal->updated_at = $time;
                $general_journal->save();

                //update Workspace for raw material purchase
                $new_total_amount = $input['total'];
                $old_total_amount = $old_main_purchase['total'];
                if ($new_total_amount > $old_total_amount) {
                    $workspace = WorkspaceLedger::where(['account_code' => 25000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += ($new_total_amount - $old_total_amount); //add material purchase
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                } elseif ($new_total_amount < $old_total_amount) {
                    $workspace = WorkspaceLedger::where(['account_code' => 25000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= ($old_total_amount - $new_total_amount); //sub material purchase
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                }
                //update cash amount
                $new_paid_amount = $input['paid'];
                $old_paid_amount = $old_main_purchase['paid'];
//                throw new \Exception($new_paid_amount.'='.$old_paid_amount);
                if (!$new_paid_amount && $old_paid_amount)// if paid amount remove
                {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 11000,
                        'year' => $year,
                        'workspace_id' => $workspace_id
                    ])->first();
                    $general_journal->delete();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= $old_paid_amount; //sub Cash
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                } elseif (!$old_paid_amount && $new_paid_amount) // if paid amount add
                {
                    //Insert data into General Journal
                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $id;
                    $journal->year = $year;
                    $journal->account_code = 11000; //Cash
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $new_paid_amount;
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= $new_paid_amount; //sub Cash
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                } elseif ($new_paid_amount > $old_paid_amount) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 11000,
                        'year' => $year,
                        'workspace_id' => $workspace_id
                    ])->first();
                    $general_journal->amount = $new_paid_amount;
                    $general_journal->updated_by = $user_id;
                    $general_journal->updated_at = $time;
                    $general_journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= ($new_paid_amount - $old_paid_amount); //sub Cash
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                } elseif ($new_paid_amount < $old_paid_amount) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 11000,
                        'year' => $year,
                        'workspace_id' => $workspace_id
                    ])->first();
                    $general_journal->amount = $new_paid_amount;
                    $general_journal->updated_by = $user_id;
                    $general_journal->updated_at = $time;
                    $general_journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += ($old_paid_amount - $new_paid_amount); //add Cash
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                }
                //DUE management (liabilities)
                $new_due_amount = $input['total'] - $input['paid'];
                $old_due_amount = $old_main_purchase['total'] - $old_main_purchase['paid'];
                if ($new_due_amount && !$old_due_amount) {
                    $journal = new GeneralJournal();
                    $journal->date = $time;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $id;
                    $journal->year = $year;
                    $journal->account_code = 41000; //Account Payable
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $new_due_amount;;
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $new_due_amount; //add account payable credit
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                    // Update Personal Account
                    $personal = PersonalAccount::where('person_id', $input['supplier_id'])->where('person_type', $person_type_supplier)->first();

                    $personal->balance += $new_due_amount;
                    $personal->updated_by = $user_id;
                    $personal->updated_at = $time;
                    $personal->save();
                } elseif ($old_due_amount && !$new_due_amount) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 41000,
                        'year' => $year,
                        'workspace_id' => $workspace_id
                    ])->first();
                    $general_journal->delete();

                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= $old_due_amount; //add account payable debit
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                    // Update Personal Account
                    $personal = PersonalAccount::where('person_id', $input['supplier_id'])->where('person_type', $person_type_supplier)->first();

                    $personal->balance -= $old_due_amount;
                    $personal->updated_by = $user_id;
                    $personal->updated_at = $time;
                    $personal->save();
                } elseif ($new_due_amount > $old_due_amount) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 41000,
                        'year' => $year,
                        'workspace_id' => $workspace_id
                    ])->first();
                    $general_journal->amount = $new_due_amount;
                    $general_journal->updated_by = $user_id;
                    $general_journal->updated_at = $time;
                    $general_journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += ($new_due_amount - $old_due_amount); //add account payable debit
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                    // Update Personal Account
                    $personal = PersonalAccount::where('person_id', $input['supplier_id'])->where('person_type', $person_type_supplier)->first();

                    $personal->balance += ($new_due_amount - $old_due_amount);
                    $personal->updated_by = $user_id;
                    $personal->updated_at = $time;
                    $personal->save();
                } elseif ($new_due_amount < $old_due_amount) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 41000,
                        'year' => $year,
                        'workspace_id' => $workspace_id
                    ])->first();
                    $general_journal->amount = $new_due_amount;
                    $general_journal->updated_by = $user_id;
                    $general_journal->updated_at = $time;
                    $general_journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= ($old_due_amount - $new_due_amount); //add account payable debit
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                    // Update Personal Account
                    $personal = PersonalAccount::where('person_id', $input['supplier_id'])->where('person_type', $person_type_supplier)->first();

                    $personal->balance -= ($old_due_amount - $new_due_amount);
                    $personal->updated_by = $user_id;
                    $personal->updated_at = $time;
                    $personal->save();
                }
            });
        } catch (\Exception $e) {
            Session()->flash('error_message', $e->getMessage());
//            Session()->flash('error_message','Purchases Update has not been Completed');
            return Redirect::back();
        }
        Session()->flash('flash_message', 'Purchases Update has been Completed');
        return redirect('purchases');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
//    public function destroy($id)
//    {
//        //
//    }
}
