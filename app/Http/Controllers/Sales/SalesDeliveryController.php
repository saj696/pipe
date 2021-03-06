<?php

namespace App\Http\Controllers\Sales;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Product;
use App\Models\SalesDeliveryDetail;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Stock;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;

class SalesDeliveryController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }


    public function index()
    {
        $salesOrders = SalesOrder::where('status', '!=', 4)->select('*')->with(['salesOrderItems' => function ($q) {
            $q->select('id', 'sales_order_id');
        }])->with(['workspaces' => function ($q) {
            $q->select('name', 'id');
        }])->orderBy('created_at', 'desc')->paginate(Config::get('common.pagination'));

        return view('sales.salesDelivery.index', compact('salesOrders'));

    }


    public function edit($id)
    {
        $productLists = DB::table('sales_order_items as sales')
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->leftJoin('sales_delivery_details as deliver', function ($join) {
                $join->on('sales.sales_order_id', '=', 'deliver.sales_order_id');
                $join->on('sales.product_id', '=', 'deliver.product_id');
            })
            ->leftJoin('stocks', function ($join) {
                $join->on('products.id', '=', 'stocks.product_id')
                    ->where('stocks.year', '=', CommonHelper::get_current_financial_year())
                    ->where('stocks.stock_type', '=', Config::get('common.balance_type_intermediate'))
                    ->where('stocks.workspace_id', '=', Auth::user()->workspace_id);
            })
            ->select('sales.*', 'products.title', 'deliver.delivered_quantity', 'stocks.quantity')
            ->where('sales.sales_order_id', $id)
            ->get();

//        dd($productLists);
        return view('sales.salesDelivery.edit')->with(compact('productLists', 'id'));
    }

    public function store(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {
                $inputs = $request->input();
                $user = Auth::user();
                $time = time();
                $year = CommonHelper::get_current_financial_year();
                $order_quantity = 0;
                $deliver_quantity = 0;
                $products = Product::whereIn('id', array_keys($inputs['quantity']))->get()->toArray();
//                dd($products);

                foreach ($inputs['quantity'] as $key => $value) {
                    if ($value > 0) {
                        $salesDelivery = SalesDeliveryDetail::firstOrNew(['sales_order_id' => $inputs['sales_order_id'], 'product_id' => $key]);
                        if ($salesDelivery->id) {
                            $salesDelivery->updated_by = $user->id;
                            $salesDelivery->updated_at = $time;
                            $d_quantity = $salesDelivery->delivered_quantity += $inputs['deliver_now'][$key];
                            $salesDelivery->last_delivered_quantity = $inputs['deliver_now'][$key];
                            $salesDelivery->save();
                        } else {
                            $salesDelivery->sales_order_id = $inputs['sales_order_id'];
                            $salesDelivery->created_by = $user->id;
                            $salesDelivery->created_at = $time;
                            $salesDelivery->status = 2; //Partial Delivery
                            $salesDelivery->product_id = $key;
                            $salesDelivery->order_quantity = $value;
                            $d_quantity = $salesDelivery->delivered_quantity = $inputs['deliver_now'][$key];
                            $salesDelivery->last_delivered_quantity = $inputs['deliver_now'][$key];
                            $salesDelivery->save();
                        }

                        if ($d_quantity == $value) {
                            $salesDelivery->status = 4; //product delivery completed
                            $salesDelivery->save();
                            $salesOrderItem = SalesOrderItem::firstOrNew(['sales_order_id' => $inputs['sales_order_id'], 'product_id' => $key]);
                            $salesOrderItem->status = 4; // Sales item Delivery Completed
                            $salesOrderItem->save();
                        } else {
                            $salesDelivery->status = 2;  //Partial Delivery
                            $salesDelivery->save();
                            $salesOrderItem = SalesOrderItem::firstOrNew(['sales_order_id' => $inputs['sales_order_id'], 'product_id' => $key]);
                            $salesOrderItem->status = 2; //Partial Delivery
                            $salesOrderItem->save();
                        }

                        $deliver_quantity += $d_quantity;
                        $order_quantity += $value;

                        $quantity_delivered = $inputs['deliver_now'][$key];
                        $stocks = Stock::where(['year' => $year, 'stock_type' => Config::get('common.balance_type_intermediate'), 'workspace_id' => $user->workspace_id, 'product_id' => $key])->first();

                        if ($inputs['unit_type'][$key] == 2) {
                            foreach ($products as $item) {
                                if ($item['id'] == $key) {
                                    $quantity_delivered = (($quantity_delivered / $item['weight']) * $item['length']);
                                    break;
                                }
                            }
                        }

                        if($stocks->quantity < $quantity_delivered){
                            Session()->flash('warning_message', 'Insufficient stock.');
                            throw new \Exception();
                        }

                        $stocks->quantity -= $quantity_delivered;
                        $stocks->updated_by = $user->id;
                        $stocks->updated_at = $time;
                        $stocks->update();
                    }

                    if ($deliver_quantity == $order_quantity) {
                        $salesOrder = SalesOrder::find($inputs['sales_order_id']);
                        $salesOrder->status = 4; // Sales Delivery Completed
                        $salesOrder->save();
                    } else {
                        $salesOrder = SalesOrder::find($inputs['sales_order_id']);
                        $salesOrder->status = 2; //Partial Delivery
                        $salesOrder->save();
                    }
                }

            });

        } catch (\Exception $e) {
            Session()->flash('error_message', 'Products delivered failed.');
            return Redirect::back();
        }
        Session()->flash('flash_message', 'Products delivered successfully.');
        return redirect('salesDelivery');
    }
}
