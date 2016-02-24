<?php

namespace App\Http\Controllers\Sales;

use App\Http\Requests\SalesOrderRequest;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class SalesOrderController extends Controller
{
    public function index()
    {
        $salesOrders=SalesOrder::select('*')->where('status',1)->with(['salesOrderItems'=>function($q){ $q->select('id','sales_order_id'); }])->with(['workspaces'=>function($q){ $q->select('name','id'); }])->paginate(Config::get('common.pagination'));

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
        dd($request->input());

        $inputs = $request->input();
        DB::beginTransaction();
        try {
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
            $time= time();

            // Update Workspace Ledger
            $workspace = WorkspaceLedger::where(['account_code' => 11000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type])->first();
            $workspace->year = date('Y');
            $workspace->balance +=$inputs['paid'];  //Add Cash
            $workspace->updated_by = $user_id;
            $workspace->updated_at = $time;
            $workspace->save();
            $workspace = WorkspaceLedger::where(['account_code' => 12000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type])->first();
            $workspace->year = date('Y');
            $workspace->balance += $inputs['due']; //Add Account Receivable
            $workspace->updated_by = $user_id;
            $workspace->updated_at = $time;
            $workspace->save();

            $workspace = WorkspaceLedger::where(['account_code' => 31000, 'workspace_id' => $workspace_id, 'balance_type' => $balance_type])->first();
            $workspace->year = date('Y');
            $workspace->balance += $inputs['total']; //Subtract Product Sales
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
            $journal->reference_id = $sales_order_id;
            $journal->year = date('Y');
            $journal->account_code = 11000;      //Cash
            $journal->workspace_id = $workspace_id;
            $journal->amount = $inputs['paid'];
            $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
            $journal->created_by = $user_id;
            $journal->created_at = $time;
            $journal->save();

            $journal = new GeneralJournal();
            $journal->date = $time;
            $journal->transaction_type = $transaction_type;
            $journal->reference_id = $sales_order_id;
            $journal->year = date('Y');
            $journal->account_code = 12000;      //Account Receivable
            $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.debit');
            $journal->workspace_id = $workspace_id;
            $journal->amount = $inputs['due'];
            $journal->created_by = $user_id;
            $journal->created_at = $time;
            $journal->save();

            $journal = new GeneralJournal();
            $journal->date = $time;
            $journal->transaction_type = $transaction_type;
            $journal->reference_id = $sales_order_id;
            $journal->year = date('Y');
            $journal->account_code = 31000;   // Account Receivable
            $journal->workspace_id = $workspace_id;
            $journal->amount = $inputs['total'];
            $journal->dr_cr_indicator = Config::get('common.debit_credit_indicator.credit');
            $journal->created_by = $user_id;
            $journal->created_at = $time;
            $journal->save();

            //Update Personal Account

            $personal = PersonalAccount::where('person_id', $inputs['customer_id'])->where('person_type', $inputs['customer_type'])->first();
            if(!empty($inputs['due']))
            {
                $personal->due += $inputs['due'];
            }
            $personal->updated_by = $user_id;
            $personal->updated_at = $time;
            $personal->save();

            if ($grand_total != $total) {
                DB::rollBack();
                Session()->flash('flash_message', 'Total amount not match with sum of product amount!');
                return Redirect::back();
            }
            DB::commit();
            Session()->flash('flash_message', 'Sales Order has been created!');
        } catch (\Exception $e) {
            DB::rollBack();
            Session()->flash('flash_message', 'Sales Order not created!');
            return Redirect::back();
        }
        return redirect('salesOrder');
    }
}
