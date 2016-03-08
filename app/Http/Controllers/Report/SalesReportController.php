<?php

namespace App\Http\Controllers\Report;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\SalesOrder;
use App\Models\SalesReturn;
use App\Models\Workspace;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SalesReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('reportPerm');
    }

    public function index()
    {
        $workspace = Workspace::where('status', '=', 1)->lists('name', 'id');
        return view('reports.salesReport.index')->with(compact('workspace'));
    }

    public function getReport(Request $request)
    {
        $this->validate($request, [
            'workspace_id' => 'required',
        ]);

        $workspace_id = $request->input('workspace_id');
        $from_date = strtotime($request->input('from_date') . ' 12:00:01 AM');
        $to_date = strtotime($request->input('to_date') . ' 11:59:59 PM');
        $sales_type = $request->input('sales_type');

        if ($sales_type == Config::get('report.sales_type.All')) {
            $salesReturns = SalesReturn::with('workspaces')
                ->where('workspace_id', '=', $workspace_id)
                ->where('date', '>=', $from_date)
                ->where('date', '<=', $to_date)
                ->get();

            $salesOrders = SalesOrder::with('workspaces')
                ->where('workspace_id', '=', $workspace_id)
                ->where('created_at', '>=', $from_date)
                ->where('created_at', '<=', $to_date)
                ->get();

            $results = [];
            $i = 0;
            foreach ($salesOrders as $salesOrder) {
                $results[$i]['date'] = date('d-m-Y', $salesOrder->created_at);
                $results[$i]['workspace'] = $salesOrder->workspaces->name;
                if ($salesOrder->status == 1) {
                    $results[$i]['sales_type'] = 'Sales: Not yet delivered';
                } elseif ($salesOrder->status == 2) {
                    $results[$i]['sales_type'] = 'Sales: Partially delivered';
                } elseif ($salesOrder->status == 4) {
                    $results[$i]['sales_type'] = 'Sales: Fully delivered';
                }

                $results[$i]['customer'] = CommonHelper::getCustomerName($salesOrder->customer_id, $salesOrder->customer_type);
                $results[$i]['total'] = $salesOrder->total;
                $results[$i]['discount'] = $salesOrder->discount;
                $results[$i]['net'] = ($salesOrder->total + $salesOrder->transport_cost) - $salesOrder->discount;
                $results[$i]['paid'] = $salesOrder->paid;
                $results[$i]['due'] = $salesOrder->due;

                $i++;
            }

            foreach ($salesReturns as $salesReturn) {
                $results[$i]['date'] = date('d-m-Y', $salesReturn->created_at);
                $results[$i]['workspace'] = $salesReturn->workspaces->name;
                $results[$i]['sales_type'] = 'Sales Returns';

                $results[$i]['customer'] = CommonHelper::getCustomerName($salesReturn->customer_id, $salesReturn->customer_type);
                $results[$i]['total'] = $salesReturn->total_amount;
                $results[$i]['discount'] = 0;
                $results[$i]['net'] = $salesReturn->total_amount;
                $results[$i]['paid'] = $salesReturn->due_paid;
                $results[$i]['due'] = $salesReturn->due;

                $i++;
            }

            $view= view('reports.salesReport.report')->with(compact('results'))->render();
            return response()->json($view);

        } elseif ($sales_type == Config::get('report.sales_type.Sales: Fully delivered')) {
            $salesOrders = SalesOrder::with('workspaces')
                ->where('workspace_id', '=', $workspace_id)
                ->where('created_at', '>=', $from_date)
                ->where('created_at', '<=', $to_date)
                ->where('status', '=', 4)
                ->get();

            $results = [];
            $i = 0;
            foreach ($salesOrders as $salesOrder) {
                $results[$i]['date'] = date('d-m-Y', $salesOrder->created_at);
                $results[$i]['workspace'] = $salesOrder->workspaces->name;
                if ($salesOrder->status == 4) {
                    $results[$i]['sales_type'] = 'Sales: Fully delivered';
                }
                $results[$i]['customer'] = CommonHelper::getCustomerName($salesOrder->customer_id, $salesOrder->customer_type);
                $results[$i]['total'] = $salesOrder->total;
                $results[$i]['discount'] = $salesOrder->discount;
                $results[$i]['net'] = ($salesOrder->total + $salesOrder->transport_cost) - $salesOrder->discount;
                $results[$i]['paid'] = $salesOrder->paid;
                $results[$i]['due'] = $salesOrder->due;

                $i++;
            }


            $view= view('reports.salesReport.report')->with(compact('results'))->render();
            return response()->json($view);

            
        } elseif ($sales_type == Config::get('report.sales_type.Sales: Partially delivered')) {
            $salesOrders = SalesOrder::with('workspaces')
                ->where('workspace_id', '=', $workspace_id)
                ->where('created_at', '>=', $from_date)
                ->where('created_at', '<=', $to_date)
                ->where('status', '=', 2)
                ->get();

            $results = [];
            $i = 0;
            foreach ($salesOrders as $salesOrder) {
                $results[$i]['date'] = date('d-m-Y', $salesOrder->created_at);
                $results[$i]['workspace'] = $salesOrder->workspaces->name;
                if ($salesOrder->status == 2) {
                    $results[$i]['sales_type'] = 'Sales: Partially delivered';
                }
                $results[$i]['customer'] = CommonHelper::getCustomerName($salesOrder->customer_id, $salesOrder->customer_type);
                $results[$i]['total'] = $salesOrder->total;
                $results[$i]['discount'] = $salesOrder->discount;
                $results[$i]['net'] = ($salesOrder->total + $salesOrder->transport_cost) - $salesOrder->discount;
                $results[$i]['paid'] = $salesOrder->paid;
                $results[$i]['due'] = $salesOrder->due;

                $i++;
            }


            $view= view('reports.salesReport.report')->with(compact('results'))->render();
            return response()->json($view);


        }elseif ($sales_type == Config::get('report.sales_type.Sales: Not yet delivered')){
            $salesOrders = SalesOrder::with('workspaces')
                ->where('workspace_id', '=', $workspace_id)
                ->where('created_at', '>=', $from_date)
                ->where('created_at', '<=', $to_date)
                ->where('status', '=', 1)
                ->get();

            $results = [];
            $i = 0;
            foreach ($salesOrders as $salesOrder) {
                $results[$i]['date'] = date('d-m-Y', $salesOrder->created_at);
                $results[$i]['workspace'] = $salesOrder->workspaces->name;
                if ($salesOrder->status == 1) {
                    $results[$i]['sales_type'] = 'Sales: Not yet delivered';
                }
                $results[$i]['customer'] = CommonHelper::getCustomerName($salesOrder->customer_id, $salesOrder->customer_type);
                $results[$i]['total'] = $salesOrder->total;
                $results[$i]['discount'] = $salesOrder->discount;
                $results[$i]['net'] = ($salesOrder->total + $salesOrder->transport_cost) - $salesOrder->discount;
                $results[$i]['paid'] = $salesOrder->paid;
                $results[$i]['due'] = $salesOrder->due;

                $i++;
            }


            $view= view('reports.salesReport.report')->with(compact('results'))->render();
            return response()->json($view);


        }elseif ($sales_type == Config::get('report.sales_type.Sales: All')){
            $salesOrders = SalesOrder::with('workspaces')
                ->where('workspace_id', '=', $workspace_id)
                ->where('created_at', '>=', $from_date)
                ->where('created_at', '<=', $to_date)
                ->get();

            $results = [];
            $i = 0;
            foreach ($salesOrders as $salesOrder) {
                $results[$i]['date'] = date('d-m-Y', $salesOrder->created_at);
                $results[$i]['workspace'] = $salesOrder->workspaces->name;
                if ($salesOrder->status == 1) {
                    $results[$i]['sales_type'] = 'Sales: Not yet delivered';
                } elseif ($salesOrder->status == 2) {
                    $results[$i]['sales_type'] = 'Sales: Partially delivered';
                } elseif ($salesOrder->status == 4) {
                    $results[$i]['sales_type'] = 'Sales: Fully delivered';
                }

                $results[$i]['customer'] = CommonHelper::getCustomerName($salesOrder->customer_id, $salesOrder->customer_type);
                $results[$i]['total'] = $salesOrder->total;
                $results[$i]['discount'] = $salesOrder->discount;
                $results[$i]['net'] = ($salesOrder->total + $salesOrder->transport_cost) - $salesOrder->discount;
                $results[$i]['paid'] = $salesOrder->paid;
                $results[$i]['due'] = $salesOrder->due;

                $i++;
            }


            $view= view('reports.salesReport.report')->with(compact('results'))->render();
            return response()->json($view);


        }elseif ($sales_type == Config::get('report.sales_type.Sales Returns')){
            $salesReturns = SalesReturn::with('workspaces')
                ->where('workspace_id', '=', $workspace_id)
                ->where('date', '>=', $from_date)
                ->where('date', '<=', $to_date)
                ->get();

            $results = [];
            $i = 0;
            foreach ($salesReturns as $salesReturn) {
                $results[$i]['date'] = date('d-m-Y', $salesReturn->created_at);
                $results[$i]['workspace'] = $salesReturn->workspaces->name;
                $results[$i]['sales_type'] = 'Sales Returns';

                $results[$i]['customer'] = CommonHelper::getCustomerName($salesReturn->customer_id, $salesReturn->customer_type);
                $results[$i]['total'] = $salesReturn->total_amount;
                $results[$i]['discount'] = 0;
                $results[$i]['net'] = $salesReturn->total_amount;
                $results[$i]['paid'] = $salesReturn->due_paid;
                $results[$i]['due'] = $salesReturn->due;

                $i++;
            }

            $view= view('reports.salesReport.report')->with(compact('results'))->render();
            return response()->json($view);
        }

    }

}
