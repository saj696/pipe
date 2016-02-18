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

class SalesDeliveryController extends Controller
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
//        dd($request->input());

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

            if ($grand_total != $total) {
                DB::rollback();
                Session()->flash('flash_message', 'Total amount not match with sum of product amount!');
                return Redirect::back();
            }
            DB::commit();
            Session()->flash('flash_message', 'Sales Order has been created!');
        } catch (\Exception $e) {
            DB::rollback();
            Session()->flash('flash_message', 'Sales Order not created!');
            return Redirect::back();
        }
        return redirect('salesOrder');
    }
}
