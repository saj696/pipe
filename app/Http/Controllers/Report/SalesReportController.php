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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class SalesReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('reportPerm');
    }

    public function index()
    {
        $workspace_id = Auth::user()->workspace_id;
        if($workspace_id==1)
        {
            $workspace = Workspace::where('status','=',1)->lists('name','id');
        }
        else
        {
            $workspace = Workspace::where(['id'=>$workspace_id])->lists('name','id');
        }
        return view('reports.salesReport.index')->with(compact('workspace'));
    }

    public function getReport(Request $request)
    {
        $this->validate($request, [
            'workspace_id' => 'required',
            'customer_id' => 'required_if:customer_type,1,2,3',
        ],
            [
                'customer_id.required_if' => 'This field is required'
            ]

        );

        $workspace_id = $request->input('workspace_id');
        $from_date = strtotime($request->input('from_date'));
        $to_date = strtotime($request->input('to_date') . ' 11:59:59 PM');
        $sales_type = $request->input('sales_type');
        $customer_type = $request->input('customer_type');


        if ($sales_type == Config::get('report.sales_type.All')) {

            if ($customer_type == 0) {
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
            } else {
                $customer_id = $request->input('customer_id');

                $salesReturns = SalesReturn::with('workspaces')
                    ->where('workspace_id', '=', $workspace_id)
                    ->where('customer_id', '=', $customer_id)
                    ->where('customer_type', '=', $customer_type)
                    ->where('date', '>=', $from_date)
                    ->where('date', '<=', $to_date)
                    ->get();

                $salesOrders = SalesOrder::with('workspaces')
                    ->where('workspace_id', '=', $workspace_id)
                    ->where('customer_id', '=', $customer_id)
                    ->where('customer_type', '=', $customer_type)
                    ->where('created_at', '>=', $from_date)
                    ->where('created_at', '<=', $to_date)
                    ->get();
            }


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

            $view = view('reports.salesReport.report')->with(compact('results'))->render();
            return response()->json($view);

        } elseif ($sales_type == Config::get('report.sales_type.Sales: Fully delivered')) {
            if ($customer_type == 0) {
                $salesOrders = SalesOrder::with('workspaces')
                    ->where('workspace_id', '=', $workspace_id)
                    ->where('created_at', '>=', $from_date)
                    ->where('created_at', '<=', $to_date)
                    ->where('status','=',4)
                    ->get();
            } else {
                $customer_id = $request->input('customer_id');
                $salesOrders = SalesOrder::with('workspaces')
                    ->where('workspace_id', '=', $workspace_id)
                    ->where('customer_id', '=', $customer_id)
                    ->where('customer_type', '=', $customer_type)
                    ->where('created_at', '>=', $from_date)
                    ->where('created_at', '<=', $to_date)
                    ->where('status','=',4)
                    ->get();
            }

//            dd($salesOrders);

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


            $view = view('reports.salesReport.report')->with(compact('results'))->render();
            return response()->json($view);


        } elseif ($sales_type == Config::get('report.sales_type.Sales: Partially delivered')) {
            if ($customer_type == 0) {
                $salesOrders = SalesOrder::with('workspaces')
                    ->where('workspace_id', '=', $workspace_id)
                    ->where('created_at', '>=', $from_date)
                    ->where('created_at', '<=', $to_date)
                    ->where('status','=',2)
                    ->get();
            } else {
                $customer_id = $request->input('customer_id');
                $salesOrders = SalesOrder::with('workspaces')
                    ->where('workspace_id', '=', $workspace_id)
                    ->where('customer_id', '=', $customer_id)
                    ->where('customer_type', '=', $customer_type)
                    ->where('created_at', '>=', $from_date)
                    ->where('created_at', '<=', $to_date)
                    ->where('status','=',2)
                    ->get();
            }

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


            $view = view('reports.salesReport.report')->with(compact('results'))->render();
            return response()->json($view);


        } elseif ($sales_type == Config::get('report.sales_type.Sales: Not yet delivered')) {
            if ($customer_type == 0) {
                $salesOrders = SalesOrder::with('workspaces')
                    ->where('workspace_id', '=', $workspace_id)
                    ->where('created_at', '>=', $from_date)
                    ->where('created_at', '<=', $to_date)
                    ->where('status','=',1)
                    ->get();
            } else {
                $customer_id = $request->input('customer_id');
                $salesOrders = SalesOrder::with('workspaces')
                    ->where('workspace_id', '=', $workspace_id)
                    ->where('customer_id', '=', $customer_id)
                    ->where('customer_type', '=', $customer_type)
                    ->where('created_at', '>=', $from_date)
                    ->where('created_at', '<=', $to_date)
                    ->where('status','=',1)
                    ->get();
            }

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


            $view = view('reports.salesReport.report')->with(compact('results'))->render();
            return response()->json($view);


        } elseif ($sales_type == Config::get('report.sales_type.Sales: All')) {
            if ($customer_type == 0) {
                $salesOrders = SalesOrder::with('workspaces')
                    ->where('workspace_id', '=', $workspace_id)
                    ->where('created_at', '>=', $from_date)
                    ->where('created_at', '<=', $to_date)
                    ->get();
            } else {
                $customer_id = $request->input('customer_id');
                $salesOrders = SalesOrder::with('workspaces')
                    ->where('workspace_id', '=', $workspace_id)
                    ->where('customer_id', '=', $customer_id)
                    ->where('customer_type', '=', $customer_type)
                    ->where('created_at', '>=', $from_date)
                    ->where('created_at', '<=', $to_date)
                    ->get();
            }

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


            $view = view('reports.salesReport.report')->with(compact('results'))->render();
            return response()->json($view);


        } elseif ($sales_type == Config::get('report.sales_type.Sales Returns')) {
            if ($customer_type == 0) {
                $salesReturns = SalesReturn::with('workspaces')
                    ->where('workspace_id', '=', $workspace_id)
                    ->where('date', '>=', $from_date)
                    ->where('date', '<=', $to_date)
                    ->get();
            } else {
                $customer_id = $request->input('customer_id');
                $salesReturns = SalesReturn::with('workspaces')
                    ->where('workspace_id', '=', $workspace_id)
                    ->where('customer_id', '=', $customer_id)
                    ->where('customer_type', '=', $customer_type)
                    ->where('date', '>=', $from_date)
                    ->where('date', '<=', $to_date)
                    ->get();
            }

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

            $view = view('reports.salesReport.report')->with(compact('results'))->render();
            return response()->json($view);
        }

    }

}
