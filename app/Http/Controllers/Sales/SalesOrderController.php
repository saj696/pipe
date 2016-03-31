<?php

namespace App\Http\Controllers\Sales;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\SalesOrderRequest;
use App\Models\Customer;
use App\Models\GeneralJournal;
use App\Models\PersonalAccount;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Stock;
use App\Models\WorkspaceLedger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class SalesOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }



    public function index()
    {
        $salesOrders = SalesOrder::select('*')->where('status', '!=', 4)->with(['salesOrderItems' => function ($q) {
            $q->select('id', 'sales_order_id');
        }])->with(['workspaces' => function ($q) {
            $q->select('name', 'id');
        }])->paginate(Config::get('common.pagination'));

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
    
        $inputs = $request->input();
        try {
            DB::transaction(function () use ($inputs) {
                $salesOrder = new SalesOrder();
                $salesOrder->workspace_id = Auth::user()->workspace_id;
                $salesOrder->customer_id = $inputs['customer_id'];
                $salesOrder->customer_type = $inputs['customer_type'];
                $grand_total = $salesOrder->total = $inputs['total'];
                $salesOrder->discount = $inputs['discount'];
                $salesOrder->transport_cost = $inputs['transport_cost'];
                $salesOrder->paid = $inputs['paid'];
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
                $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                $workspace->balance += $inputs['paid'];  //Add Cash
                $workspace->updated_by = $user_id;
                $workspace->updated_at = $time;
                $workspace->save();
                $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                $workspace->balance += $inputs['due']; //Add Account Receivable
                $workspace->updated_by = $user_id;
                $workspace->updated_at = $time;
                $workspace->save();

                $workspace = WorkspaceLedger::where(['account_code' => 31000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                $workspace->balance += $inputs['total']; //Add Product Sales
                $workspace->updated_by = $user_id;
                $workspace->updated_at = $time;
                $workspace->save();

                /*// Update General Ledger
                $general = GeneralLedger::where(['account_code' => 11000, 'balance_type' => $balance_type])->first();
                $general->year = $year;
                $general->balance -= ($inputs['total'] - $inputs['due_paid']); //Subtract Cash
                $general->updated_by = $user_id;
                $general->updated_at = $time;
                $general->save();

                $general = GeneralLedger::where(['account_code' => 32000, 'balance_type' => $balance_type])->first();
                $general->year = $year;
                $general->balance += ($inputs['total'] - $inputs['due_paid']); //Add Product Sales Return
                $general->updated_by = $user_id;
                $general->updated_at = $time;
                $general->save();

                $general = GeneralLedger::where(['account_code' => 12000, 'balance_type' => $balance_type])->first();
                $general->year = $year;
                $general->balance -= $inputs['due_paid']; //Subtract Cash
                $general->updated_by = $user_id;
                $general->updated_at = $time;
                $general->save();*/

                //Insert data into General Journal

                $journal = new GeneralJournal();
                $journal->date =$date;
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

                $journal = new GeneralJournal();
                $journal->date =$date;
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

                $journal = new GeneralJournal();
                $journal->date =$date;
                $journal->transaction_type = $transaction_type;
                $journal->reference_id = $sales_order_id;
                $journal->year = $year;
                $journal->account_code = 31000;   // Account Receivable
                $journal->workspace_id = $workspace_id;
                $journal->amount = $inputs['total'];
                $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                $journal->created_by = $user_id;
                $journal->created_at = $time;
                $journal->save();

                //Update Personal Account

                $personal = PersonalAccount::where('person_id', $inputs['customer_id'])->where('person_type', $inputs['customer_type'])->first();
                if (!empty($inputs['due'])) {
                    $personal->due += $inputs['due'];
                }
                $personal->updated_by = $user_id;
                $personal->updated_at = $time;
                $personal->save();

                if ($grand_total != $total) {
                    throw new \Exception("error");
                }
            });
        } catch (\Exception $e) {
            Session()->flash('flash_message', 'Sales Order not created!');
            return Redirect::back();
        }

        Session()->flash('flash_message', 'Sales Order has been created!');
        return redirect('salesOrder');
    }

    public function edit($id)
    {
        $customers = Customer::where('status', 1)->lists('name', 'id');
        $salesOrder = SalesOrder::where('id', $id)->where('status', '!=', 4)->with(['salesOrderItems', 'salesOrderItems.product'])->first();
//        dd($salesOrder);
        return view('sales.salesOrder.edit')->with(compact('salesOrder', 'customers'));
    }

    public function update($id, SalesOrderRequest $request)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $inputs = $request->input();
                $user = Auth::user();
                $time = time();
                $year = CommonHelper::get_current_financial_year();

                $salesOrder = SalesOrder::find($id);
                $oldSalesOrder = clone $salesOrder;
                $salesOrder->total = $inputs['total'];
                $salesOrder->discount = $inputs['discount'];
                $salesOrder->paid = $inputs['paid'];
                $salesOrder->due = $inputs['total'] - $inputs['paid'];
                $salesOrder->transport_cost = $inputs['transport_cost'];
                $salesOrder->remarks = $inputs['remarks'];
                $salesOrder->updated_by = $user->id;
                $salesOrder->updated_at = $time;
                $salesOrder->update();

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
                        $salesOrderItem->unit_price = $product['unit_price'];
                        $salesOrderItem->updated_by = $user->id;
                        $salesOrderItem->updated_at = $time;
                        $salesOrderItem->update();

                        if ($product['sales_quantity'] > $arrange_old_items[$product['product_id']]['sales_quantity']) {
                            //Update Product stock
                            $stock = Stock::where(['year' => $year, 'stock_type' => Config::get('common.balance_type_intermediate'), 'workspace_id' => $user->workspace_id, 'product_id' => $product['product_id']])->first();
                            $stock->quantity -= ($product['sales_quantity'] - $arrange_old_items[$product['id']]['sales_quantity']);
                            $stock->updated_by = $user->id;
                            $stock->updated_at = $time;
                            $stock->update();
                        } else {
                            //Update Product stock
                            $stock = Stock::where(['year' => $year, 'stock_type' => Config::get('common.balance_type_intermediate'), 'workspace_id' => $user->workspace_id, 'product_id' => $product['product_id']])->first();
                            $stock->quantity += ($arrange_old_items[$product['product_id']]['sales_quantity'] - $product['sales_quantity']);
                            $stock->updated_by = $user->id;
                            $stock->updated_at = $time;
                            $stock->update();
                        }

                    } else {
                        $salesOrderItem = new SalesOrderItem();
                        $salesOrderItem->sales_order_id = $id;
                        $salesOrderItem->product_id = $product['product_id'];
                        $salesOrderItem->sales_quantity = $product['sales_quantity'];
                        $salesOrderItem->unit_price = $product['unit_price'];
                        $salesOrderItem->created_by = $user->id;
                        $salesOrderItem->created_at = $time;
                        $salesOrderItem->status = 1;
                        $salesOrderItem->save();

                        //Update Product stock
                        $stock = Stock::where(['year' => $year, 'stock_type' => Config::get('common.balance_type_intermediate'), 'workspace_id' => $user->workspace_id, 'product_id' => $product['product_id']])->first();
                        $stock->quantity -= $product['sales_quantity'];
                        $stock->updated_by = $user->id;
                        $stock->updated_at = $time;
                        $stock->update();

                    }

                    unset($arrange_old_items[$product['product_id']]);
                }

                foreach ($arrange_old_items as $old_item) {
                    //Update Product stock
                    $stock = Stock::where(['year' => $year, 'stock_type' => Config::get('common.balance_type_intermediate'), 'workspace_id' => $user->workspace_id, 'product_id' => $old_item['product_id']])->first();
                    $stock->quantity += $old_item['sales_quantity'];
                    $stock->updated_by = $user->id;
                    $stock->updated_at = $time;
                    $stock->update();

                    $salesOrderItem = SalesOrderItem::findOrFail($old_item['id']);
                    $salesOrderItem->delete();
                }

                $balance_type = Config::get('common.balance_type_intermediate');
                $transaction_type = Config::get('common.transaction_type.sales');
                $new_total_amount = $inputs['total'];
                $old_total_amount = $oldSalesOrder['total'];

                if ($new_total_amount > $old_total_amount) {
                    $workspace = WorkspaceLedger::where(['account_code' => 31000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->year = $year;
                    $workspace->balance += ($new_total_amount - $old_total_amount); //Add Product Sales
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                } elseif ($new_total_amount < $old_total_amount) {
                    $workspace = WorkspaceLedger::where(['account_code' => 31000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->year = $year;
                    $workspace->balance -= ($old_total_amount - $new_total_amount); //Subtract Product Sales
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
                    $workspace->balance -= $old_paid_amount; //sub Cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                } elseif (!$old_paid_amount && $new_paid_amount) // if paid amount add
                {
                    //Insert data into General Journal
                    $journal = new GeneralJournal();
                    $journal->date =$date;
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
                    $workspace->balance += $new_paid_amount; //sub Cash
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
                    $workspace->balance -= ($old_paid_amount - $new_paid_amount); //sub Cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->save();
                }

                $new_due_amount = $inputs['total'] - $inputs['paid'];
                $old_due_amount = $oldSalesOrder['total'] - $oldSalesOrder['paid'];
                if ($new_due_amount && !$old_due_amount) {
                    $journal = new GeneralJournal();
                    $journal->date =$date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $id;
                    $journal->year = date('Y');
                    $journal->account_code = 12000; //Account Receivable
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->workspace_id = $user->workspace_id;
                    $journal->amount = $new_due_amount;;
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
            });
        } catch (\Exception $e) {
            dd($e);
            Session()->flash('error_message', 'Sales Order not Updated!');
            return Redirect::back();
        }

        Session()->flash('flash_message', 'Sales Order has been Updated!');
        return redirect('salesOrder');
    }
}
