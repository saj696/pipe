<?php

namespace App\Http\Controllers\Sales;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Customer;
use App\Models\Defect;
use App\Models\DefectItem;
use App\Models\Employee;
use App\Models\GeneralJournal;
use App\Models\Material;
use App\Models\PersonalAccount;
use App\Models\RawStock;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\WorkspaceLedger;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;

class ReceiveDefectController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
        $this->middleware('transactionPermission', ['except' => ['index']]);
    }

    public function index()
    {
        $defects = Defect::orderBy('created_at', 'desc')->paginate(15);
        return view('sales.receiveDefect.index')->with(compact('defects'));
    }

    public function create()
    {
        $customers = Customer::where('status', 1)->lists('name', 'id');
        return view('sales.receiveDefect.create')->with('customers', $customers);
    }

    public function store(Request $request)
    {

        //Validation
        $this->validate($request, [
            'customer_type' => 'required',
            'customer_id' => 'required',
            'product' => 'required||array',
            'total' => 'required',
        ]);


//        dd($request->input());

        try {
            DB::transaction(function () use ($request) {
                $inputs = $request->input();
                $user = Auth::user();
                $time = time();
                $date = strtotime(date('d-m-Y'));
                $year = CommonHelper::get_current_financial_year();
                $transaction_type = Config::get('common.transaction_type.defect_receive');
                $balance_type = Config::get('common.balance_type_intermediate');

                //Defect table affected.
                $defect = new Defect();
                $defect->customer_type = $inputs['customer_type'];
                $defect->customer_id = $inputs['customer_id'];
                $defect->workspace_id = $user->workspace_id;
                $defect->total = $inputs['total'];
                $defect->cash = $inputs['cash'];
                $defect->due_paid = $inputs['due_paid'];
                $defect->due = $inputs['due'];
                if (isset($inputs['is_replacement'])) {
                    $defect->is_replacement = $inputs['is_replacement'];
                    $defect->replacement = $inputs['new_total'];
                }

                $defect->date = $time;
                $defect->remarks = $inputs['remarks'];
                $defect->created_by = $user->id;
                $defect->created_at = $time;
                $defect->save();

                //Get Scrap Id
                $material = Material::where('name', '=', 'Scrap')->where('status', '=', 1)->first();

                //Defect Items affected
                foreach ($inputs['product'] as $product) {
                    $defectItem = new DefectItem();
                    $defectItem->defect_id = $defect->id;
                    $defectItem->product_id = $product['product_id'];
                    $defectItem->quantity = $product['receive_quantity'];
                    $defectItem->unit_type = $product['unit_type'];
                    $defectItem->unit_price = $product['unit_price'];
                    $defectItem->created_by = $user->id;
                    $defectItem->created_at = $time;
                    $defectItem->save();

                    //Material stock updated
                    $rawStock = RawStock::where('year', '=', $year)->where('stock_type', '=', Config::get('common.balance_type_intermediate'))->where('material_id', '=', $material->id)->first();
                    if ($product['unit_type'] == 1) {
                        $rawStock->quantity += (($product['receive_quantity'] / $product['length']) * $product['weight']);
                    } else {
                        $rawStock->quantity += $product['receive_quantity'];
                    }
                    $rawStock->updated_by = $user->id;
                    $rawStock->updated_at = $time;
                    $rawStock->update();
                }

                $defect_amount = $inputs['cash'] + $inputs['due_paid'] + $inputs['due'];

                if ($defect_amount > 0) {
                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $defect->id;
                    $journal->year = $year;
                    $journal->account_code = 36000; //Defect Receive
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
                    $journal->workspace_id = $user->workspace_id;
                    $journal->amount = $defect_amount;
                    $journal->created_by = $user->id;
                    $journal->created_at = $time;
                    $journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 36000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $defect_amount; //add defect receive
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();
                }


                if ($inputs['cash'] > 0) { //Cash

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    if ($workspace->balance < $inputs['cash']) {
                        Session()->flash('warning_message', 'Insufficient cash balance!.');
                        throw new \Exception();
                    }
                    $workspace->balance -= $inputs['cash']; //sub cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $defect->id;
                    $journal->year = $year;
                    $journal->account_code = 11000; //Cash
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->workspace_id = $user->workspace_id;
                    $journal->amount = $inputs['cash'];
                    $journal->created_by = $user->id;
                    $journal->created_at = $time;
                    $journal->save();


                }

                if ($inputs['due_paid'] > 0) { //Due Pay
                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $defect->id;
                    $journal->year = $year;
                    $journal->account_code = 12000; //Account Receivable
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->workspace_id = $user->workspace_id;
                    $journal->amount = $inputs['due_paid'];
                    $journal->created_by = $user->id;
                    $journal->created_at = $time;
                    $journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= $inputs['due_paid']; //sub account receivable
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $personalAccount = PersonalAccount::where('person_type', '=', $inputs['customer_type'])->where('person_id', '=', $inputs['customer_id'])->first();
                    $personalAccount->due -= $inputs['due_paid']; //Sub due
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->update();
                }

                if ($inputs['due'] > 0) {// Due

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $defect->id;
                    $journal->year = $year;
                    $journal->account_code = 41000; //Account Payable
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->workspace_id = $user->workspace_id;
                    $journal->amount = $inputs['due'];
                    $journal->created_by = $user->id;
                    $journal->created_at = $time;
                    $journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $inputs['due']; //add account payable
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                }

                if (isset($inputs['is_replacement'])) { //Replacement

                    $defectReplacement = new SalesOrder();
                    $defectReplacement->workspace_id = $user->workspace_id;
                    $defectReplacement->defect_id = $defect->id;
                    $defectReplacement->order_type = Config::get('common.sales_order_type.replacement');
                    $defectReplacement->customer_id = $inputs['customer_id'];
                    $defectReplacement->customer_type = $inputs['customer_type'];
                    $defectReplacement->total = $inputs['new_total'];
                    $defectReplacement->date = $time;
                    $defectReplacement->delivery_status = 1;
                    $defectReplacement->created_by = $user->id;
                    $defectReplacement->created_at = $time;
                    $defectReplacement->save();

                    foreach ($inputs['new_product'] as $new_product) {
                        $defectReplacementItem = new SalesOrderItem();
                        $defectReplacementItem->sales_order_id = $defectReplacement->id;
                        $defectReplacementItem->product_id = $new_product['product_id'];
                        $defectReplacementItem->sales_quantity = $new_product['sales_quantity'];
                        $defectReplacementItem->sales_unit_type = $new_product['sales_unit_type'];
                        $defectReplacementItem->unit_price = $new_product['unit_price'];
                        $defectReplacementItem->created_by = $user->id;
                        $defectReplacementItem->created_at = $time;
                        $defectReplacementItem->save();

                        $stock = Stock::where('product_id', '=', $new_product['product_id'])->where('stock_type', '=', $balance_type)->where('year', '=', $year)->first();
                        $stock = Stock::where('product_id', '=', $new_product['product_id'])->where('stock_type', '=', $balance_type)->where('year', '=', $year)->first();
                        if ($new_product['sales_unit_type'] == 1) {

                            if ($stock->quantity < $new_product['sales_quantity']) {
                                Session()->flash('warning_message', 'Insufficient stock!.');
                                throw new \Exception();
                            }

                            $stock->quantity -= $new_product['sales_quantity']; //Sub stock
                        } elseif ($new_product['sales_unit_type'] == 2) {
                            $stock->quantity -= (($new_product['sales_quantity'] / $new_product['weight']) * $new_product['length']); //Sub stock
                        }

                        $stock->updated_by = $user->id;
                        $stock->updated_at = $time;
                        $stock->update();
                    }
                }
            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Defect receive create not success. Please try again.');
            return Redirect::back();
        }

        Session()->flash('flash_message', 'Defect receive created successfully.');
        return redirect('receive_defect');
    }

    public function edit($id)
    {
        $defect = Defect::where('id', '=', $id)->with('defectItems.product')->first();
//        dd($defect);
        if ($defect->customer_type == 1) {
            $customers = Employee::where('status', 1)->lists('name', 'id');
        } elseif ($defect->customer_type == 2) {
            $customers = Supplier::where('status', 1)->lists('company_name', 'id');
        } else {
            $customers = Customer::where('status', 1)->lists('name', 'id');
        }

        $salesOrder = new SalesOrder();

        if ($defect->is_replacement) {
            $salesOrder = SalesOrder::where('defect_id', '=', $defect->id)->with(['salesOrderItems', 'salesOrderItems.product', 'salesOrderItems.salesDelivery'])->first();
        }

        $personalAccount = PersonalAccount::where('person_type', '=', $defect->customer_type)->where('person_id', '=', $defect->customer_id)->first();

        return view('sales.receiveDefect.edit')->with(compact('defect', 'customers', 'personalAccount', 'salesOrder'));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'product' => 'array',
            'total' => 'required',
        ]);

//        dd($request->input());

        try {
            DB::transaction(function () use ($id, $request) {
                $inputs = $request->input();
                $user = Auth::user();
                $time = time();
                $date = strtotime(date('d-m-Y'));
                $year = CommonHelper::get_current_financial_year();
                $transaction_type = Config::get('common.transaction_type.defect_receive');
                $balance_type = Config::get('common.balance_type_intermediate');

                //Defect table affected.
                $defect = Defect::find($id);
                $oldDefect = clone $defect;
                $defect->total = $inputs['total'];
                $defect->cash = $inputs['cash'];
                $defect->due_paid = $inputs['due_paid'];
                $defect->due = $inputs['due'];
                if (isset($inputs['is_replacement'])) {
                    $defect->is_replacement = $inputs['is_replacement'];
                    $defect->replacement = $inputs['new_total'];
                }
                $defect->remarks = $inputs['remarks'];
                $defect->updated_by = $user->id;
                $defect->updated_at = $time;
                $defect->update();

                $defect_amount = $inputs['cash'] + $inputs['due_paid'] + $inputs['due'];

                if ($defect_amount > 0) {
                    $journal = GeneralJournal::where('account_code', '=', 36000)->where('workspace_id', '=', $user->workspace_id)->where('reference_id', '=', $id)->where('transaction_type', '=', $transaction_type)->where('year', '=', $year)->first();
                    $journal->amount = $defect_amount;
                    $journal->updated_by = $user->id;
                    $journal->updated_at = $time;
                    $journal->update();

                    $workspace = WorkspaceLedger::where(['account_code' => 36000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    if ($oldDefect->total > $defect_amount) {
                        $workspace->balance += ($oldDefect->total - $defect_amount); //sub defect receive
                    } elseif ($oldDefect->total < $defect_amount) {
                        $workspace->balance += ($defect_amount - $oldDefect->total); //sub defect receive
                    }
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();
                } else {
                    $journal = GeneralJournal::where('account_code', '=', 36000)->where('workspace_id', '=', $user->workspace_id)->where('reference_id', '=', $id)->where('transaction_type', '=', $transaction_type)->where('year', '=', $year)->first();
                    $journal->delete();

                    $workspace = WorkspaceLedger::where(['account_code' => 36000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= ($oldDefect->cash + $oldDefect->due_paid + $oldDefect->due); //sub defect receive
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();
                }


                if (isset($inputs['delete_product'])) {
                    foreach ($inputs['delete_product'] as $product) {
                        $defectItem = DefectItem::where('defect_id', '=', $id)->where('product_id', '=', $product['product_id'])->first();
                        if ($defectItem) {
                            $defectItem->delete();
                        }

                    }
                }

                //Get Scrap Id
                $material = Material::where('name', '=', 'Scrap')->where('status', '=', 1)->first();

                //Old Defect Items
                foreach ($inputs['old_product'] as $product) {
                    $defectItem = DefectItem::where('defect_id', '=', $id)->where('product_id', '=', $product['product_id'])->first();
                    $oldDefectItem = clone $defectItem;
                    $defectItem->quantity = $product['receive_quantity'];
                    $defectItem->unit_type = $product['unit_type'];
                    $defectItem->unit_price = $product['unit_price'];
                    $defectItem->updated_by = $user->id;
                    $defectItem->updated_at = $time;
                    $defectItem->update();

                    //Material stock updated
                    $rawStock = RawStock::where('year', '=', $year)->where('stock_type', '=', Config::get('common.balance_type_intermediate'))->where('material_id', '=', $material->id)->first();
                    if ($product['unit_type'] == 1) {

                        if ($oldDefectItem->unit_type == 1) {

                            if ($oldDefectItem->quantity > $product['receive_quantity']) {
                                $rawStock->quantity -= ((($oldDefectItem->quantity - $product['receive_quantity']) / $product['length']) * $product['weight']);
                            } elseif ($oldDefectItem->quantity < $product['receive_quantity']) {
                                $rawStock->quantity += ((($product['receive_quantity'] - $oldDefectItem->quantity) / $product['length']) * $product['weight']);
                            }

                        } elseif ($oldDefectItem->unit_type == 2) {

                            $old_quantity = ($oldDefectItem->quantity / $product['weight']) * $product['length'];

                            if ($old_quantity > $product['receive_quantity']) {
                                $rawStock->quantity -= ((($old_quantity - $product['receive_quantity']) / $product['length']) * $product['weight']);
                            } elseif ($old_quantity < $product['receive_quantity']) {
                                $rawStock->quantity += ((($product['receive_quantity'] - $old_quantity) / $product['length']) * $product['weight']);
                            }
                        }

                    } elseif ($product['unit_type'] == 2) {

                        if ($oldDefectItem->unit_type == 1) {

                            $old_quantity = ($oldDefectItem->quantity / $product['length']) * $product['weight'];

                            if ($old_quantity > $product['receive_quantity']) {
                                $rawStock->quantity -= ($old_quantity - $product['receive_quantity']);
                            } elseif ($old_quantity < $product['receive_quantity']) {
                                $rawStock->quantity += ($product['receive_quantity'] - $old_quantity);
                            }
                        } elseif ($oldDefectItem->unit_type == 2) {

                            if ($oldDefectItem->quantity > $product['receive_quantity']) {
                                $rawStock->quantity -= ($oldDefectItem->quantity - $product['receive_quantity']);
                            } elseif ($oldDefectItem->quantity < $product['receive_quantity']) {
                                $rawStock->quantity += ($product['receive_quantity'] - $oldDefectItem->quantity);
                            }
                        }
                    }
                    $rawStock->updated_by = $user->id;
                    $rawStock->updated_at = $time;
                    $rawStock->update();
                }

                //New Defect Items
                if (!empty($inputs['product'])) {
                    foreach ($inputs['product'] as $product) {
                        $defectItem = new DefectItem();
                        $defectItem->defect_id = $id;
                        $defectItem->product_id = $product['product_id'];
                        $defectItem->quantity = $product['receive_quantity'];
                        $defectItem->unit_type = $product['unit_type'];
                        $defectItem->unit_price = $product['unit_price'];
                        $defectItem->created_by = $user->id;
                        $defectItem->created_at = $time;
                        $defectItem->save();

                        //Material stock updated
                        $rawStock = RawStock::where('year', '=', $year)->where('stock_type', '=', Config::get('common.balance_type_intermediate'))->where('material_id', '=', $material->id)->first();
                        if ($product['unit_type'] == 1) {
                            $rawStock->quantity += (($product['receive_quantity'] / $product['length']) * $product['weight']);
                        } else {
                            $rawStock->quantity += $product['receive_quantity'];
                        }
                        $rawStock->updated_by = $user->id;
                        $rawStock->updated_at = $time;
                        $rawStock->update();
                    }
                }

                if ($inputs['cash'] && !$oldDefect->cash) { //Cash

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $id;
                    $journal->year = $year;
                    $journal->account_code = 11000; //Cash
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->workspace_id = $user->workspace_id;
                    $journal->amount = $inputs['cash'];
                    $journal->created_by = $user->id;
                    $journal->created_at = $time;
                    $journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= ($inputs['cash'] - $oldDefect->cash); //sub cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                } elseif (!$inputs['cash'] && $oldDefect->cash) { //Cash

                    $journal = GeneralJournal::where('account_code', '=', 11000)->where('workspace_id', '=', $user->workspace_id)->where('reference_id', '=', $id)->where('transaction_type', '=', $transaction_type)->where('year', '=', $year)->first();
                    $journal->delete();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $oldDefect->cash; //add cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                } elseif ($inputs['cash'] > $oldDefect->cash) {

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    if ($workspace->balance < ($inputs['cash'] - $oldDefect->cash)) {
                        Session()->flash('warning_message', 'Insufficient cash balance!.');
                        throw new \Exception();
                    }
                    $workspace->balance -= ($inputs['cash'] - $oldDefect->cash); //sub cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $journal = GeneralJournal::where('account_code', '=', 11000)->where('workspace_id', '=', $user->workspace_id)->where('reference_id', '=', $id)->where('transaction_type', '=', $transaction_type)->where('year', '=', $year)->first();
                    $journal->amount = $inputs['cash'];
                    $journal->updated_by = $user->id;
                    $journal->updated_at = $time;
                    $journal->update();

                } elseif ($inputs['cash'] < $oldDefect->cash) {

                    $journal = GeneralJournal::where('account_code', '=', 11000)->where('workspace_id', '=', $user->workspace_id)->where('reference_id', '=', $id)->where('transaction_type', '=', $transaction_type)->where('year', '=', $year)->first();
                    $journal->amount = $inputs['cash'];
                    $journal->updated_by = $user->id;
                    $journal->updated_at = $time;
                    $journal->update();

                    $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += ($oldDefect->cash - $inputs['cash']); //add cash
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();
                }

                if ($inputs['due_paid'] && !$oldDefect->due_paid) { //Due Pay
                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $defect->id;
                    $journal->year = $year;
                    $journal->account_code = 12000; //Account Receivable
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->workspace_id = $user->workspace_id;
                    $journal->amount = $inputs['due_paid'];
                    $journal->created_by = $user->id;
                    $journal->created_at = $time;
                    $journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= $inputs['due_paid']; //sub account receivable
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $personalAccount = PersonalAccount::where('person_type', '=', $oldDefect->customer_type)->where('person_id', '=', $inputs['customer_id'])->first();
                    $personalAccount->due -= $inputs['due_paid']; //Sub due
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->update();

                } elseif (!$inputs['due_paid'] && $oldDefect->due_paid) {

                    $journal = GeneralJournal::where('account_code', '=', 12000)->where('workspace_id', '=', $user->workspace_id)->where('reference_id', '=', $id)->where('transaction_type', '=', $transaction_type)->where('year', '=', $year)->first();
                    $journal->delete();

                    $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $oldDefect->due_paid; //add account receivable
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $personalAccount = PersonalAccount::where('person_type', '=', $oldDefect->customer_type)->where('person_id', '=', $inputs['customer_id'])->first();
                    $personalAccount->due += $oldDefect->due_paid; //add due
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->update();

                } elseif ($inputs['due_paid'] > $oldDefect->due_paid) {

                    $journal = GeneralJournal::where('account_code', '=', 12000)->where('workspace_id', '=', $user->workspace_id)->where('reference_id', '=', $id)->where('transaction_type', '=', $transaction_type)->where('year', '=', $year)->first();
                    $journal->amount = $inputs['due_paid'];
                    $journal->updated_by = $user->id;
                    $journal->updated_at = $time;
                    $journal->update();

                    $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= ($inputs['due_paid'] - $oldDefect->due_paid); //sub account receivable
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $personalAccount = PersonalAccount::where('person_type', '=', $oldDefect->customer_type)->where('person_id', '=', $inputs['customer_id'])->first();
                    $personalAccount->due -= ($inputs['due_paid'] - $oldDefect->due_paid); //Sub due
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->update();

                } elseif ($inputs['due_paid'] < $oldDefect->due_paid) {

                    $journal = GeneralJournal::where('account_code', '=', 12000)->where('workspace_id', '=', $user->workspace_id)->where('reference_id', '=', $id)->where('transaction_type', '=', $transaction_type)->where('year', '=', $year)->first();
                    $journal->amount = $inputs['due_paid'];
                    $journal->updated_by = $user->id;
                    $journal->updated_at = $time;
                    $journal->update();

                    $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += ($oldDefect->due_paid - $inputs['due_paid']); //add account receivable
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $personalAccount = PersonalAccount::where('person_type', '=', $oldDefect->customer_type)->where('person_id', '=', $oldDefect->customer_id)->first();
                    $personalAccount->due += ($oldDefect->due_paid - $inputs['due_paid']); //add due
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->update();

                }

                if ($inputs['due'] && !$oldDefect->due) {// Due

                    $journal = new GeneralJournal();
                    $journal->date = $date;
                    $journal->transaction_type = $transaction_type;
                    $journal->reference_id = $defect->id;
                    $journal->year = $year;
                    $journal->account_code = 41000; //Account Payable
                    $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
                    $journal->workspace_id = $user->workspace_id;
                    $journal->amount = $inputs['due'];
                    $journal->created_by = $user->id;
                    $journal->created_at = $time;
                    $journal->save();

                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += $inputs['due']; //add account payable
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $personalAccount = PersonalAccount::where('person_type', '=', $oldDefect->customer_type)->where('person_id', '=', $inputs['customer_id'])->first();
                    $personalAccount->balance += $inputs['due']; //Add Balance
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->update();

                } elseif (!$inputs['due'] && $oldDefect->due) {// Due

                    $journal = GeneralJournal::where('account_code', '=', 41000)->where('workspace_id', '=', $user->workspace_id)->where('reference_id', '=', $id)->where('transaction_type', '=', $transaction_type)->where('year', '=', $year)->first();
                    $journal->delete();

                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= $oldDefect->due; //sub account payable
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $personalAccount = PersonalAccount::where('person_type', '=', $oldDefect->customer_type)->where('person_id', '=', $inputs['customer_id'])->first();
                    $personalAccount->balance -= $oldDefect->due; //Sub Balance
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->update();

                } elseif ($inputs['due'] > $oldDefect->due) {// Due

                    $journal = GeneralJournal::where('account_code', '=', 41000)->where('workspace_id', '=', $user->workspace_id)->where('reference_id', '=', $id)->where('transaction_type', '=', $transaction_type)->where('year', '=', $year)->first();
                    $journal->amount = $inputs['due'];
                    $journal->updated_by = $user->id;
                    $journal->updated_at = $time;
                    $journal->update();

                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance += ($inputs['due'] - $oldDefect->due); //add account payable
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $personalAccount = PersonalAccount::where('person_type', '=', $oldDefect->customer_type)->where('person_id', '=', $inputs['customer_id'])->first();
                    $personalAccount->balance += ($inputs['due'] - $oldDefect->due); //Add Balance
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->update();

                } elseif ($inputs['due'] < $oldDefect->due) {// Due

                    $journal = GeneralJournal::where('account_code', '=', 41000)->where('workspace_id', '=', $user->workspace_id)->where('reference_id', '=', $id)->where('transaction_type', '=', $transaction_type)->where('year', '=', $year)->first();
                    $journal->amount = $inputs['due'];
                    $journal->updated_by = $user->id;
                    $journal->updated_at = $time;
                    $journal->update();

                    $workspace = WorkspaceLedger::where(['account_code' => 41000, 'workspace_id' => $user->workspace_id, 'balance_type' => $balance_type, 'year' => $year])->first();
                    $workspace->balance -= ($oldDefect->due - $inputs['due']); //sub account payable
                    $workspace->updated_by = $user->id;
                    $workspace->updated_at = $time;
                    $workspace->update();

                    $personalAccount = PersonalAccount::where('person_type', '=', $oldDefect->customer_type)->where('person_id', '=', $inputs['customer_id'])->first();
                    $personalAccount->balance -= ($oldDefect->due - $inputs['due']); //sub Balance
                    $personalAccount->updated_by = $user->id;
                    $personalAccount->updated_at = $time;
                    $personalAccount->update();

                }


                if (isset($inputs['is_replacement']) && !$oldDefect->is_replacement) { //Replacement

                    $defectReplacement = new SalesOrder();
                    $defectReplacement->workspace_id = $user->workspace_id;
                    $defectReplacement->defect_id = $defect->id;
                    $defectReplacement->order_type = Config::get('common.sales_order_type.replacement');
                    $defectReplacement->customer_id = $inputs['customer_id'];
                    $defectReplacement->customer_type = $oldDefect->customer_type;
                    $defectReplacement->total = $inputs['new_total'];
                    $defectReplacement->date = $time;
                    $defectReplacement->delivery_status = 1;
                    $defectReplacement->created_by = $user->id;
                    $defectReplacement->created_at = $time;
                    $defectReplacement->save();

                    foreach ($inputs['new_product'] as $new_product) {
                        $defectReplacementItem = new SalesOrderItem();
                        $defectReplacementItem->sales_order_id = $defectReplacement->id;
                        $defectReplacementItem->product_id = $new_product['product_id'];
                        $defectReplacementItem->sales_quantity = $new_product['sales_quantity'];
                        $defectReplacementItem->sales_unit_type = $new_product['sales_unit_type'];
                        $defectReplacementItem->unit_price = $new_product['unit_price'];
                        $defectReplacementItem->created_by = $user->id;
                        $defectReplacementItem->created_at = $time;
                        $defectReplacementItem->save();

                        $stock = Stock::where('product_id', '=', $new_product['product_id'])->where('stock_type', '=', $balance_type)->where('year', '=', $year)->first();

                        if ($new_product['sales_unit_type'] == 1) {

                            if ($stock->quantity < $new_product['sales_quantity']) {
                                Session()->flash('warning_message', 'Insufficient stock!.');
                                throw new \Exception();
                            }

                            $stock->quantity -= $new_product['sales_quantity']; //Sub stock

                        } elseif ($new_product['sales_unit_type'] == 2) {

                            $sales_quantity = (($new_product['sales_quantity'] / $new_product['weight']) * $new_product['length']);

                            if ($stock->quantity < $sales_quantity) {
                                Session()->flash('warning_message', 'Insufficient stock!.');
                                throw new \Exception();
                            }

                            $stock->quantity -= $sales_quantity; //Sub stock
                        }
                        $stock->updated_by = $user->id;
                        $stock->updated_at = $time;
                        $stock->update();
                    }


                } elseif (isset($inputs['is_replacement']) && $oldDefect->is_replacement) {

                    $defectReplacement = SalesOrder::where('defect_id', '=', $id)->first();
                    $oldDefectReplacement = clone $defectReplacement;
                    $defectReplacement->total = $inputs['new_total'];
                    $defectReplacement->updated_by = $user->id;
                    $defectReplacement->updated_at = $time;
                    $defectReplacement->update();

                    if (isset($inputs['delete_replacement_product'])) {
                        foreach ($inputs['delete_replacement_product'] as $product) {
                            $defectReplacementItem = SalesOrderItem::where('sales_order_id', '=', $defectReplacement->id)->where('product_id', '=', $product['product_id'])->first();
                            if ($defectReplacementItem) {
                                $defectReplacementItem->delete();
                            }


                        }
                    }

                    //Old Product
                    foreach ($inputs['old_replacement_product'] as $new_product) {
                        $defectReplacementItem = SalesOrderItem::where('sales_order_id', '=', $defectReplacement->id)->where('product_id', '=', $new_product['product_id'])->first();
                        $oldDefectReplacementItem = clone $defectReplacementItem;
                        $defectReplacementItem->sales_quantity = $new_product['sales_quantity'];
                        $defectReplacementItem->sales_unit_type = $new_product['sales_unit_type'];
                        $defectReplacementItem->unit_price = $new_product['unit_price'];
                        $defectReplacementItem->updated_by = $user->id;
                        $defectReplacementItem->updated_at = $time;
                        $defectReplacementItem->update();

                        $stock = Stock::where('product_id', '=', $new_product['product_id'])->where('stock_type', '=', $balance_type)->where('year', '=', $year)->first();

                        if ($oldDefectReplacementItem->sales_unit_type == 1) {

                            if ($new_product['sales_unit_type'] == 1) {

                                if ($oldDefectReplacementItem->sales_quantity > $new_product['sales_quantity']) {
                                    $stock->quantity += ($oldDefectReplacementItem->sales_quantity - $new_product['sales_quantity']); //Add stock
                                } elseif ($oldDefectReplacementItem->sales_quantity < $new_product['sales_quantity']) {

                                    $sales_quantity = ($new_product['sales_quantity'] - $oldDefectReplacementItem->sales_quantity);

                                    if ($stock->quantity < $sales_quantity) {
                                        Session()->flash('warning_message', 'Insufficient stock!.');
                                        throw new \Exception();
                                    }

                                    $stock->quantity -= $sales_quantity; //Sub stock
                                }

                            } elseif ($new_product['sales_unit_type'] == 2) {

                                $new_sales_quantity = ($new_product['sales_quantity'] / $new_product['weight']) * $new_product['length'];

                                if ($oldDefectReplacementItem->sales_quantity > $new_sales_quantity) {
                                    $stock->quantity += ($oldDefectReplacementItem->sales_quantity - $new_sales_quantity); //Add stock
                                } elseif ($oldDefectReplacementItem->sales_quantity < $new_sales_quantity) {

                                    $sales_quantity = ($new_sales_quantity - $oldDefectReplacementItem->sales_quantity);

                                    if ($stock->quantity < $sales_quantity) {
                                        Session()->flash('warning_message', 'Insufficient stock!.');
                                        throw new \Exception();
                                    }

                                    $stock->quantity -= $sales_quantity; //Sub stock
                                }
                            }

                        } elseif ($oldDefectReplacementItem->sales_unit_type == 2) {

                            if ($new_product['sales_unit_type'] == 1) {

                                $new_sales_quantity = ($oldDefectReplacementItem->sales_quantity / $new_product['weight']) * $new_product['length'];

                                if ($new_sales_quantity > $new_product['sales_quantity']) {

                                    $stock->quantity += ($new_sales_quantity - $new_product['sales_quantity']);
                                    $stock->quantity += ($new_sales_quantity - $new_product['sales_quantity']); //Add stock

                                } elseif ($new_sales_quantity < $new_product['sales_quantity']) {

                                    $sales_quantity = ($new_product['sales_quantity'] - $new_sales_quantity); //Sub stock

                                    if ($stock->quantity < $sales_quantity) {
                                        Session()->flash('warning_message', 'Insufficient stock!.');
                                        throw new \Exception();
                                    }

                                    $stock->quantity -= $sales_quantity; //Sub stock
                                }

                            } elseif ($new_product['sales_unit_type'] == 2) {

                                $old_sales_quantity = ($oldDefectReplacementItem->sales_quantity / $new_product['weight']) * $new_product['length'];
                                $new_sales_quantity = ($new_product['sales_quantity'] / $new_product['weight']) * $new_product['length'];

                                if ($old_sales_quantity > $new_sales_quantity) {
                                    $stock->quantity += ($old_sales_quantity - $new_sales_quantity); //Add stock
                                } elseif ($old_sales_quantity < $new_sales_quantity) {

                                    $sales_quantity = ($new_sales_quantity - $old_sales_quantity);

                                    if ($stock->quantity < $sales_quantity) {
                                        Session()->flash('warning_message', 'Insufficient stock!.');
                                        throw new \Exception();
                                    }

                                    $stock->quantity -= $sales_quantity; //Sub stock
                                }
                            }
                        }


                        $stock->updated_by = $user->id;
                        $stock->updated_at = $time;
                        $stock->update();
                    }

                    //New Product
                    if (!empty($inputs['new_product'])) {
                        foreach ($inputs['new_product'] as $new_product) {
                            $defectReplacementItem = new SalesOrderItem();
                            $defectReplacementItem->sales_order_id = $defectReplacement->id;
                            $defectReplacementItem->product_id = $new_product['product_id'];
                            $defectReplacementItem->sales_quantity = $new_product['sales_quantity'];
                            $defectReplacementItem->sales_unit_type = $new_product['sales_unit_type'];
                            $defectReplacementItem->unit_price = $new_product['unit_price'];
                            $defectReplacementItem->created_by = $user->id;
                            $defectReplacementItem->created_at = $time;
                            $defectReplacementItem->save();

                            $stock = Stock::where('product_id', '=', $new_product['product_id'])->where('stock_type', '=', $balance_type)->where('year', '=', $year)->first();
                            $stock->quantity -= $new_product['sales_quantity']; //Sub stock
                            $stock->updated_by = $user->id;
                            $stock->updated_at = $time;
                            $stock->update();
                        }
                    }
                } elseif (!isset($inputs['is_replacement']) && $oldDefect->is_replacement) {

                    $defectReplacement = SalesOrder::where('defect_id', '=', $id)->first();
                    $oldDefectReplacement = clone $defectReplacement;
                    $defectReplacement->total = $inputs['new_total'];
                    $defectReplacement->updated_by = $user->id;
                    $defectReplacement->updated_at = $time;
                    $defectReplacement->update();

                    if (isset($inputs['delete_replacement_product'])) {
                        foreach ($inputs['delete_replacement_product'] as $product) {
                            $defectReplacementItem = SalesOrderItem::where('sales_order_id', '=', $defectReplacement->id)->where('product_id', '=', $product['product_id'])->first();
                            if ($defectReplacementItem) {
                                $defectReplacementItem->delete();
                            }

                        }
                    }

                    //Old Product
                    foreach ($inputs['old_replacement_product'] as $new_product) {
                        $defectReplacementItem = SalesOrderItem::where('sales_order_id', '=', $defectReplacement->id)->where('product_id', '=', $new_product['product_id'])->first();
                        $oldDefectReplacementItem = clone $defectReplacementItem;
                        $defectReplacementItem->sales_quantity = $new_product['sales_quantity'];
                        $defectReplacementItem->sales_unit_type = $new_product['sales_unit_type'];
                        $defectReplacementItem->unit_price = $new_product['unit_price'];
                        $defectReplacementItem->updated_by = $user->id;
                        $defectReplacementItem->updated_at = $time;
                        $defectReplacementItem->update();

                        $stock = Stock::where('product_id', '=', $new_product['product_id'])->where('stock_type', '=', $balance_type)->where('year', '=', $year)->first();

                        if ($oldDefectReplacementItem->sales_unit_type == 1) {

                            if ($new_product['sales_unit_type'] == 1) {

                                if ($oldDefectReplacementItem->sales_quantity > $new_product['sales_quantity']) {
                                    $stock->quantity += ($oldDefectReplacementItem->sales_quantity - $new_product['sales_quantity']); //Add stock
                                } elseif ($oldDefectReplacementItem->sales_quantity < $new_product['sales_quantity']) {

                                    $sales_quantity = ($new_product['sales_quantity'] - $oldDefectReplacementItem->sales_quantity);

                                    if ($stock->quantity < $sales_quantity) {
                                        Session()->flash('warning_message', 'Insufficient stock!.');
                                        throw new \Exception();
                                    }

                                    $stock->quantity -= $sales_quantity; //Sub stock
                                }

                            } elseif ($new_product['sales_unit_type'] == 2) {

                                $new_sales_quantity = ($new_product['sales_quantity'] / $new_product['weight']) * $new_product['length'];

                                if ($oldDefectReplacementItem->sales_quantity > $new_sales_quantity) {
                                    $stock->quantity += ($oldDefectReplacementItem->sales_quantity - $new_sales_quantity); //Add stock
                                } elseif ($oldDefectReplacementItem->sales_quantity < $new_sales_quantity) {

                                    $sales_quantity = ($new_sales_quantity - $oldDefectReplacementItem->sales_quantity);

                                    if ($stock->quantity < $sales_quantity) {
                                        Session()->flash('warning_message', 'Insufficient stock!.');
                                        throw new \Exception();
                                    }

                                    $stock->quantity -= $sales_quantity; //Sub stock
                                }
                            }

                        } elseif ($oldDefectReplacementItem->sales_unit_type == 2) {

                            if ($new_product['sales_unit_type'] == 1) {

                                $new_sales_quantity = ($oldDefectReplacementItem->sales_quantity / $new_product['weight']) * $new_product['length'];

                                if ($new_sales_quantity > $new_product['sales_quantity']) {

                                    $stock->quantity += ($new_sales_quantity - $new_product['sales_quantity']);
                                    $stock->quantity += ($new_sales_quantity - $new_product['sales_quantity']); //Add stock

                                } elseif ($new_sales_quantity < $new_product['sales_quantity']) {

                                    $sales_quantity = ($new_product['sales_quantity'] - $new_sales_quantity); //Sub stock

                                    if ($stock->quantity < $sales_quantity) {
                                        Session()->flash('warning_message', 'Insufficient stock!.');
                                        throw new \Exception();
                                    }

                                    $stock->quantity -= $sales_quantity; //Sub stock
                                }

                            } elseif ($new_product['sales_unit_type'] == 2) {

                                $old_sales_quantity = ($oldDefectReplacementItem->sales_quantity / $new_product['weight']) * $new_product['length'];
                                $new_sales_quantity = ($new_product['sales_quantity'] / $new_product['weight']) * $new_product['length'];

                                if ($old_sales_quantity > $new_sales_quantity) {
                                    $stock->quantity += ($old_sales_quantity - $new_sales_quantity); //Add stock
                                } elseif ($old_sales_quantity < $new_sales_quantity) {

                                    $sales_quantity = ($new_sales_quantity - $old_sales_quantity);

                                    if ($stock->quantity < $sales_quantity) {
                                        Session()->flash('warning_message', 'Insufficient stock!.');
                                        throw new \Exception();
                                    }

                                    $stock->quantity -= $sales_quantity; //Sub stock
                                }
                            }
                        }


                        $stock->updated_by = $user->id;
                        $stock->updated_at = $time;
                        $stock->update();
                    }
                }
            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Defect receive update not success. Please try again.');
            return Redirect::back();
        }

        Session()->flash('flash_message', 'Defect receive updated successfully.');
        return redirect('receive_defect');
    }

}
