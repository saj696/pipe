<?php

namespace App\Http\Controllers\Sales;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\SalesOrderRequest;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Supplier;
use App\Models\WorkspaceLedger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class SalesOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm', ['except' => ['invoice_print']]);
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }


    public function index()
    {
        $salesOrders = SalesOrder::select('*')->where('defect_id', '=', null)->where('status', '!=', 4)->with(['salesOrderItems' => function ($q) {
            $q->select('id', 'sales_order_id');
        }])->with(['workspaces' => function ($q) {
            $q->select('name', 'id');
        }])->orderBy('created_at', 'desc')
            ->paginate(Config::get('common.pagination'));

//        dd($salesOrders);
        return view('sales.salesOrder.index', compact('salesOrders'));

    }


    public function create()
    {
        $customers = Customer::where('status', 1)->lists('name', 'id');
        return view('sales.salesOrder.create')->with('customers', $customers);
    }

    public function store(SalesOrderRequest $request)
    {
        $sales_order_id = "";
        $inputs = $request->input();
        try {
            DB::transaction(function () use ($inputs, &$sales_order_id) {
                if (empty($inputs['transport_cost'])) {
                    $inputs['transport_cost'] = 0;
                }
                if (empty($inputs['labour_cost'])) {
                    $inputs['labour_cost'] = 0;
                }

                if (empty($inputs['discount'])) {
                    $inputs['discount'] = 0;
                }
                if (empty($inputs['paid'])) {
                    $inputs['paid'] = 0;
                }

                if (!isset($inputs['paid_from_personal_account'])) {
                    $inputs['paid_from_personal_account'] = 0;
                }

                $salesOrder = new SalesOrder();
                $salesOrder->workspace_id = Auth::user()->workspace_id;
                $salesOrder->customer_id = $inputs['customer_id'];
                $salesOrder->customer_type = $inputs['customer_type'];
                $salesOrder->total = $inputs['total'] - $inputs['discount'];
                $salesOrder->discount = $inputs['discount'];
                $salesOrder->transport_cost = $inputs['transport_cost'];
                $salesOrder->labour_cost = $inputs['labour_cost'];
                $salesOrder->paid = $inputs['paid'];
                $salesOrder->personal_account_paid = $inputs['paid_from_personal_account'];
                $salesOrder->due = $inputs['due'];
                $salesOrder->delivery_status = 1;
                $salesOrder->created_by = Auth::user()->id;
                $salesOrder->created_at = time();
                $salesOrder->status = 1;
                $salesOrder->save();
                $sales_order_id = $salesOrder->id;
                unset($data);

                $total = 0;
                foreach ($inputs['product'] as $product) {
                    $salesOderItems = new SalesOrderItem();
                    $salesOderItems->sales_order_id = $sales_order_id;
                    $salesOderItems->product_id = $product['product_id'];
                    $salesOderItems->sales_quantity = $product['sales_quantity'];
                    $salesOderItems->sales_unit_type = $product['sales_unit_type'];
                    $salesOderItems->unit_price = $product['unit_price'];
                    $salesOderItems->created_by = Auth::user()->id;
                    $salesOderItems->created_at = time();
                    $salesOderItems->status = 1;
                    $total += $product['sales_quantity'] * $product['unit_price'];
                    $salesOderItems->save();

                    unset($data);
                }

                $user_id = Auth::user()->id;
                $workspace_id = Auth::user()->workspace_id;
                $balance_type = Config::get('common.balance_type_intermediate');
                $transaction_type = Config::get('common.transaction_type.sales');
                $time = time();
                $date = strtotime(date('d-m-Y'));
                $year = CommonHelper::get_current_financial_year();


                // Update Workspace Ledger
                if ($inputs['transport_cost'] > 0) {
                    $workspace = WorkspaceLedger::where(['account_code' => 35000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $inputs['transport_cost'];  //Add Transportation Earning
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_order_id;
                    $journal->year = $year;
                    $journal->account_code = 35000;   // Transportation Earning
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['transport_cost'];
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();
                }

                if ($inputs['labour_cost'] > 0) {
                    $workspace = WorkspaceLedger::where(['account_code' => 34000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $inputs['labour_cost'];  //Add Labour Earning
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_order_id;
                    $journal->year = $year;
                    $journal->account_code = 34000;   // Labour Earning
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['labour_cost'];
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();
                }

                if ($inputs['paid'] > 0) {
                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $inputs['paid'];  //Add Cash
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_order_id;
                    $journal->year = $year;
                    $journal->account_code = 11000;      //Cash
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['paid'];
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();
                }


                if ($inputs['due'] > 0) {
                    $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $inputs['due']; //Add Account Receivable
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->update();
                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $sales_order_id;
                    $journal->year = $year;
                    $journal->account_code = 12000;      //Account Receivable
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['due'];
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();
                }

                $workspace = WorkspaceLedger::where(['account_code' => 31000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                $workspace->balance += ($inputs['total'] - $inputs['discount']); //Add Product Sales
                $workspace->updated_by = $user_id;
                $workspace->updated_at = $time;
                $workspace->update();

                $journal = new GeneralJournal();
                $journal->date = $date;
                $journal->transaction_type = $transaction_type;
                $journal->reference_id = $sales_order_id;
                $journal->year = $year;
                $journal->account_code = 31000;   // Product Sales
                $journal->workspace_id = $workspace_id;
                $journal->amount = $inputs['total'] - $inputs['discount'];
                $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                $journal->created_by = $user_id;
                $journal->created_at = $time;
                $journal->save();

                //Update Personal Account
                if ($inputs['due'] > 0) {
                    $personal = PersonalAccount::where('person_id', $inputs['customer_id'])->where('person_type', $inputs['customer_type'])->first();
                    $personal->due += $inputs['due'];
                    $personal->updated_by = $user_id;
                    $personal->updated_at = $time;
                    $personal->update();
                }

                if ($inputs['paid_from_personal_account'] > 0) {
                    $personal = PersonalAccount::where('person_id', $inputs['customer_id'])->where('person_type', $inputs['customer_type'])->first();
                    $personal->balance -= $inputs['paid_from_personal_account'];
                    $personal->updated_by = $user_id;
                    $personal->updated_at = $time;
                    $personal->update();

                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= $inputs['paid_from_personal_account']; //Sub Account Payable
                    $workspace->updated_by = $user_id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $salesOrder->id;
                    $journal->year = $year;
                    $journal->account_code = 41000;   // Account Payable
                    $journal->workspace_id = $workspace_id;
                    $journal->amount = $inputs['paid_from_personal_account'];
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->created_by = $user_id;
                    $journal->created_at = $time;
                    $journal->save();
                }
            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Sales Order not created!');
            return Redirect::back();
        }

        Session()->flash('flash_message', 'Sales Order has been created!');
        return redirect()->action('Sales\SalesOrderController@invoice_print', [$sales_order_id]);
    }

    public function edit($id)
    {

        $salesOrder = SalesOrder::where('id', $id)->where('status', '!=', 4)->with(['salesOrderItems', 'salesOrderItems.product', 'salesOrderItems.salesDelivery'])->first();
        if ($salesOrder->customer_type == 1) {
            $customers = Employee::where('status', 1)->lists('name', 'id');
        } elseif ($salesOrder->customer_type == 2) {
            $customers = Supplier::where('status', 1)->lists('name', 'id');
        } else {
            $customers = Customer::where('status', 1)->lists('name', 'id');
        }

        if ($salesOrder->customer_id) {
            $personalAccount = PersonalAccount::where('person_id', '=', $salesOrder->customer_id)->where('person_type', '=', $salesOrder->customer_type)->first();
        }

        return view('sales.salesOrder.edit')->with(compact('salesOrder', 'customers', 'personalAccount'));
    }

    public function update($id, SalesOrderRequest $request)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $inputs = $request->input();
                $user = Auth::user();
                $time = time();
                $date = strtotime(date('d-m-Y'));
                $year = CommonHelper::get_current_financial_year();

                if (empty($inputs['transport_cost'])) {
                    $inputs['transport_cost'] = 0;
                }
                if (empty($inputs['labour_cost'])) {
                    $inputs['labour_cost'] = 0;
                }

                if (empty($inputs['discount'])) {
                    $inputs['discount'] = 0;
                }

                if (!isset($inputs['paid_from_personal_account'])) {
                    $inputs['paid_from_personal_account'] = 0;
                }


                $salesOrder = SalesOrder::find($id);
                $oldSalesOrder = clone $salesOrder;
                $salesOrder->total = $inputs['total'] - $inputs['discount'];
                $salesOrder->discount = $inputs['discount'];
                $salesOrder->paid = $inputs['paid'];
                $salesOrder->personal_account_paid = $inputs['paid_from_personal_account'];
                $salesOrder->due = $inputs['due'];
                $salesOrder->transport_cost = $inputs['transport_cost'];
                $salesOrder->labour_cost = $inputs['labour_cost'];
                $salesOrder->remarks = $inputs['remarks'];
                $salesOrder->updated_by = $user->id;
                $salesOrder->updated_at = $time;
                $salesOrder->update();

                if (isset($inputs['delete_product'])) {
                    foreach ($inputs['delete_product'] as $product) {
                        $salesOrderItem = SalesOrderItem::where('sales_order_id', '=', $id)->where('product_id', '=', $product['product_id'])->first();
                        if ($salesOrderItem) {
                            $salesOrderItem->delete();
                        }
                    }
                }

                $old_products = SalesOrderItem::where('sales_order_id', '=', $id)->where('status', '!=', 4)->get();
                $arrange_old_items = [];
                foreach ($old_products as $old_product) {
                    $arrange_old_items[$old_product['product_id']] = $old_product;
                }
                foreach ($inputs['product'] as $product) {
                    if (isset($arrange_old_items[$product['product_id']]))//if old data
                    {
                        $salesOrderItem = SalesOrderItem::find($arrange_old_items[$product['product_id']]['id']);
                        $salesOrderItem->sales_quantity = $product['sales_quantity'];
                        $salesOrderItem->sales_unit_type = $product['sales_unit_type'];
                        $salesOrderItem->unit_price = $product['unit_price'];
                        $salesOrderItem->updated_by = $user->id;
                        $salesOrderItem->updated_at = $time;
                        $salesOrderItem->update();
                    } else {
                        $salesOrderItem = new SalesOrderItem();
                        $salesOrderItem->sales_order_id = $id;
                        $salesOrderItem->product_id = $product['product_id'];
                        $salesOrderItem->sales_quantity = $product['sales_quantity'];
                        $salesOrderItem->sales_unit_type = $product['sales_unit_type'];
                        $salesOrderItem->unit_price = $product['unit_price'];
                        $salesOrderItem->created_by = $user->id;
                        $salesOrderItem->created_at = $time;
                        $salesOrderItem->status = 1;
                        $salesOrderItem->save();
                    }

                    unset($arrange_old_items[$product['product_id']]);
                }

                $balance_type = Config::get('common.balance_type_intermediate');
                $transaction_type = Config::get('common.transaction_type.sales');
                $new_total_amount = $inputs['total'];
                $old_total_amount = $oldSalesOrder['total'];

                if ($new_total_amount > $old_total_amount) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 31000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id
                    ])->first();
                    $general_journal->amount = $new_total_amount - $inputs['discount'];
                    $general_journal->updated_by = $user->id;
                    $general_journal->updated_at = $time;
                    $general_journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 31000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->year = $year;
                    $workspace->balance += ($new_total_amount - $old_total_amount - $inputs['discount']); //Add Product Sales
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                } elseif ($new_total_amount < $old_total_amount) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 31000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id
                    ])->first();
                    $general_journal->amount = $new_total_amount - $inputs['discount'];
                    $general_journal->updated_by = $user->id;
                    $general_journal->updated_at = $time;
                    $general_journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 31000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->year = $year;
                    $workspace->balance -= ($old_total_amount - ($new_total_amount - $inputs['discount'])); //Subtract Product Sales
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                }

                $new_paid_amount = $inputs['paid'];
                $old_paid_amount = $oldSalesOrder['paid'];

                if (!$new_paid_amount && $old_paid_amount)// if paid amount remove
                {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 11000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id

                    ])->first();
                    $general_journal->delete();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    if ($workspace->balance < $old_paid_amount) {
                        Session()->flash('warning_message', 'Insufficient cash balance!');
                        throw new \Exception();
                    }
                    $workspace->balance -= $old_paid_amount; //sub Cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                } elseif (!$old_paid_amount && $new_paid_amount) // if paid amount add
                {
                    //Insert data into General Journal
                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $id;
                    $journal->year = $year;
                    $journal->account_code = 11000; //Cash
                    $journal->workspace_id = $user->workspace_id;
                    $journal->amount = $new_paid_amount;
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->created_by = $user->id;
                    $journal->created_at = $time;
                    $journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->year = $year;
                    $workspace->balance += $new_paid_amount; //Add Cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                } elseif ($new_paid_amount > $old_paid_amount) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 11000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id
                    ])->first();
                    $general_journal->amount = $new_paid_amount;
                    $general_journal->updated_by = $user->id;
                    $general_journal->updated_at = $time;
                    $general_journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += ($new_paid_amount - $old_paid_amount); //Add Cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                } elseif ($new_paid_amount < $old_paid_amount) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 11000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id
                    ])->first();
                    $general_journal->amount = $new_paid_amount;
                    $general_journal->updated_by = $user->id;
                    $general_journal->updated_at = $time;
                    $general_journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    if ($workspace->balance < ($old_paid_amount - $new_paid_amount)) {
                        Session()->flash('warning_message', 'Insufficient cash balance!');
                        throw new \Exception();
                    }

                    $workspace->balance -= ($old_paid_amount - $new_paid_amount); //sub Cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                    dd($workspace);
                }

                $new_account_paid = $inputs['paid_from_personal_account'];
                $old_account_paid = $oldSalesOrder->personal_account_paid;

                if (!$new_account_paid && $old_account_paid) {
                    $personal = PersonalAccount::where('person_id', $inputs['customer_id'])->where('person_type', $inputs['customer_type'])->first();
                    $personal->balance += $old_account_paid;
                    $personal->updated_by = $user->id;
                    $personal->updated_at = $time;
                    $personal->update();

                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $old_account_paid; //Add Account Payable
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 41000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id
                    ])->first();
                    $general_journal->delete();
                } elseif ($new_account_paid && !$old_account_paid) {
                    $personal = PersonalAccount::where('person_id', $inputs['customer_id'])->where('person_type', $inputs['customer_type'])->first();
                    $personal->balance -= $new_account_paid;
                    $personal->updated_by = $user->id;
                    $personal->updated_at = $time;
                    $personal->update();

                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= $new_account_paid; //Add Account Payable
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $id;
                    $journal->year = $year;
                    $journal->account_code = 41000; //Account Payable
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->workspace_id = $user->workspace_id;
                    $journal->amount = $new_account_paid;;
                    $journal->created_by = $user->id;
                    $journal->created_at = $time;
                    $journal->save();

                } elseif ($new_account_paid > $old_account_paid) {

                    $personal = PersonalAccount::where('person_id', $salesOrder->customer_id)->where('person_type', $salesOrder->customer_type)->first();
                    $personal->balance -= ($new_account_paid - $old_account_paid);
                    $personal->updated_by = $user->id;
                    $personal->updated_at = $time;
                    $personal->update();

                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= ($new_account_paid - $old_account_paid); //Sub Account Payable
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 41000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id
                    ])->first();
                    $general_journal->amount = $new_account_paid;
                    $general_journal->updated_by = $user->id;
                    $general_journal->updated_at = $time;
                    $general_journal->update();

                } elseif ($new_account_paid < $old_account_paid) {

                    $personal = PersonalAccount::where('person_id', $salesOrder->customer_id)->where('person_type', $salesOrder->customer_type)->first();
                    $personal->balance += ($old_account_paid - $new_account_paid);
                    $personal->updated_by = $user->id;
                    $personal->updated_at = $time;
                    $personal->update();

                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += ($old_account_paid - $new_account_paid); //Add Account Payable
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 41000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id
                    ])->first();
                    $general_journal->amount = $new_account_paid;
                    $general_journal->updated_by = $user->id;
                    $general_journal->updated_at = $time;
                    $general_journal->update();
                }


                $new_due_amount = $inputs['due'];
                $old_due_amount = $oldSalesOrder['due'];
                if ($new_due_amount && !$old_due_amount) {
                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $id;
                    $journal->year = $year;
                    $journal->account_code = 12000; //Account Receivable
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->workspace_id = $user->workspace_id;
                    $journal->amount = $new_due_amount;
                    $journal->created_by = $user->id;
                    $journal->created_at = $time;
                    $journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $new_due_amount; //add account receivable debit
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                    // Update Personal Account
                    $personal = PersonalAccount::where('person_id', $oldSalesOrder['customer_id'])->where('person_type', $oldSalesOrder['customer_type'])->first();
                    $personal->due += $new_due_amount;
                    $personal->updated_by = $user->id;
                    $personal->updated_at = $time;
                    $personal->save();
                } elseif ($old_due_amount && !$new_due_amount) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 12000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id
                    ])->first();
                    $general_journal->delete();

                    $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= $old_due_amount; //sub account receivable credit
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    // Update Personal Account
                    $personal = PersonalAccount::where('person_id', $oldSalesOrder['customer_id'])->where('person_type', $oldSalesOrder['customer_type'])->first();
                    $personal->due -= $old_due_amount;
                    $personal->updated_by = $user->id;
                    $personal->updated_at = $time;
                    $personal->save();
                } elseif ($new_due_amount > $old_due_amount) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 12000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id
                    ])->first();
                    $general_journal->amount = $new_due_amount;
                    $general_journal->updated_by = $user->id;
                    $general_journal->updated_at = $time;
                    $general_journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += ($new_due_amount - $old_due_amount); //add account receivable debit
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                    // Update Personal Account
                    $personal = PersonalAccount::where('person_id', $oldSalesOrder['customer_id'])->where('person_type', $oldSalesOrder['customer_type'])->first();

                    $personal->due += ($new_due_amount - $old_due_amount);
                    $personal->updated_by = $user->id;
                    $personal->updated_at = $time;
                    $personal->save();
                } elseif ($new_due_amount < $old_due_amount) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 12000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id
                    ])->first();
                    $general_journal->amount = $new_due_amount;
                    $general_journal->updated_by = $user->id;
                    $general_journal->updated_at = $time;
                    $general_journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= ($old_due_amount - $new_due_amount); //add account receivable credit
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                    // Update Personal Account
                    $personal = PersonalAccount::where('person_id', $oldSalesOrder['customer_id'])->where('person_type', $oldSalesOrder['customer_type'])->first();

                    $personal->due -= ($old_due_amount - $new_due_amount);
                    $personal->updated_by = $user->id;
                    $personal->updated_at = $time;
                    $personal->save();
                }

                $new_transportation_cost = $inputs['transport_cost'];
                $old_transportation_cost = $oldSalesOrder['transport_cost'];
                if ($new_transportation_cost <= 0 && $old_transportation_cost) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 35000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id
                    ])->first();
                    $general_journal->delete();

                    $workspace = WorkspaceLedger::where(['account_code' => 35000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= $old_transportation_cost; //sub transportation earning
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    if ($workspace->balance < $old_transportation_cost) {
                        Session()->flash('warning_message', 'Insufficient cash balance!');
                        throw new \Exception();
                    }
                    $workspace->balance -= $old_transportation_cost;  //Sub Cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();


                } elseif ($new_transportation_cost && $old_transportation_cost <= 0) {
                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $id;
                    $journal->year = $year;
                    $journal->account_code = 35000; //Account Receivable
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->workspace_id = $user->workspace_id;
                    $journal->amount = $new_transportation_cost;;
                    $journal->created_by = $user->id;
                    $journal->created_at = $time;
                    $journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 35000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $new_transportation_cost; //add transportation earning
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $new_transportation_cost;  //Add Cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                } elseif ($new_transportation_cost > $old_transportation_cost) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 35000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id
                    ])->first();
                    $general_journal->amount = $new_transportation_cost;
                    $general_journal->updated_by = $user->id;
                    $general_journal->updated_at = $time;
                    $general_journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 35000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += ($new_transportation_cost - $old_transportation_cost); //Add transportation earning
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += ($new_transportation_cost - $old_transportation_cost);  //Add Cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                } elseif ($new_transportation_cost < $old_transportation_cost) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 35000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id
                    ])->first();
                    $general_journal->amount = $new_transportation_cost;
                    $general_journal->updated_by = $user->id;
                    $general_journal->updated_at = $time;
                    $general_journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 35000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= ($old_transportation_cost - $new_transportation_cost); //Add transportation earning
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    if ($workspace->balance < ($old_transportation_cost - $new_transportation_cost)) {
                        Session()->flash('warning_message', 'Insufficient cash balance!');
                        throw new \Exception();
                    }
                    $workspace->balance -= ($old_transportation_cost - $new_transportation_cost);  //Sub Cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();
                }

                $new_labour_cost = $inputs['labour_cost'];
                $old_labour_cost = $oldSalesOrder['labour_cost'];
                if ($new_labour_cost <= 0 && $old_labour_cost) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 34000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id
                    ])->first();
                    $general_journal->delete();

                    $workspace = WorkspaceLedger::where(['account_code' => 34000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= $old_labour_cost; //sub labour earning
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    if ($workspace->balance < $old_labour_cost) {
                        Session()->flash('warning_message', 'Insufficient cash balance!');
                        throw new \Exception();
                    }
                    $workspace->balance -= $old_labour_cost;  //Sub Cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                } elseif ($new_labour_cost && $old_labour_cost <= 0) {
                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $id;
                    $journal->year = $year;
                    $journal->account_code = 34000; //labour earning
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->workspace_id = $user->workspace_id;
                    $journal->amount = $new_labour_cost;;
                    $journal->created_by = $user->id;
                    $journal->created_at = $time;
                    $journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 34000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $new_labour_cost; //add labour earning
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $new_labour_cost;  //Add Cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                } elseif ($new_labour_cost > $old_labour_cost) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 34000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id
                    ])->first();
                    $general_journal->amount = $new_labour_cost;
                    $general_journal->updated_by = $user->id;
                    $general_journal->updated_at = $time;
                    $general_journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 34000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += ($new_labour_cost - $old_labour_cost); //Add transportation earning
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += ($new_labour_cost - $old_labour_cost);  //Add Cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                } elseif ($new_labour_cost < $old_labour_cost) {
                    $general_journal = GeneralJournal::where([
                        'transaction_type' => $transaction_type,
                        'reference_id' => $id,
                        'account_code' => 34000,
                        'year' => $year,
                        'workspace_id' => $user->workspace_id
                    ])->first();
                    $general_journal->amount = $new_labour_cost;
                    $general_journal->updated_by = $user->id;
                    $general_journal->updated_at = $time;
                    $general_journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 34000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= ($old_labour_cost - $new_labour_cost); //Add labour earning
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    if ($workspace->balance < ($old_labour_cost - $new_labour_cost)) {
                        Session()->flash('warning_message', 'Insufficient cash balance!');
                        throw new \Exception();
                    }
                    $workspace->balance -= ($old_labour_cost - $new_labour_cost);  //Sub Cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();
                }
            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Sales Order not Updated!');
            return Redirect::back();
        }

        Session()->flash('flash_message', 'Sales Order has been Updated!');
        return redirect('salesOrder');
    }

    public function invoice_print($id)
    {
        $salesOrder = SalesOrder::where('id', $id)->where('status', '!=', 4)->with(['salesOrderItems', 'salesOrderItems.product'])->first();
        return view('sales.salesOrder.invoice')->with(compact('salesOrder'));
    }
}
