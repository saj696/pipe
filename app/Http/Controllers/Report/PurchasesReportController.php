<?php

namespace App\Http\Controllers\Report;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Supplier;
use Config;
use DB;
use Illuminate\Http\Request;

class PurchasesReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('reportPerm');
    }

    public function index()
    {
        $suppliers = Supplier::where('status', '=', 1)->lists('company_name', 'id');
        return view('reports.purchasesReport.index')->with(compact('suppliers'));
    }

    public function getReport(Request $request)
    {
        $from_date = strtotime($request->input('from_date'));
        $to_date = strtotime($request->input('to_date') . ' 11:59:59 PM');
        $supplier_id = $request->input('supplier_id');
        $purchase_type = $request->input('purchase_type');

        if ($purchase_type == Config::get('report.purchase_type.All')) {

            $results = [];
            $i = 0;

            if($supplier_id==0)
            {
                $purchases = DB::table('purchases')
                    ->where('purchase_date', '>=', $from_date)
                    ->where('purchase_date', '<=', $to_date)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }else{
                $purchases = DB::table('purchases')
                    ->where('supplier_id', '=', $supplier_id)
                    ->where('purchase_date', '>=', $from_date)
                    ->where('purchase_date', '<=', $to_date)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }


            foreach ($purchases as $purchase) {
                $results[$i]['date'] = date('d-m-Y', $purchase->purchase_date);
                $results[$i]['purchase_type'] = 'Purchases';
                $results[$i]['supplier'] = CommonHelper::getCustomerName($purchase->supplier_id, 2);
                $results[$i]['total'] = $purchase->total;
                $results[$i]['transport_cost'] = $purchase->transportation_cost;
                $results[$i]['net'] = $purchase->transportation_cost + $purchase->total;
                $results[$i]['paid'] = $purchase->paid;
                $results[$i]['due'] = ($purchase->transportation_cost + $purchase->total) - $purchase->paid;
                $i++;
            }

            if($supplier_id==0)
            {
                $purchasesReturns = DB::table('purchases_return')
                    ->where('purchase_return_date', '>=', $from_date)
                    ->where('purchase_return_date', '<=', $to_date)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }else
            {
                $purchasesReturns = DB::table('purchases_return')
                    ->where('supplier_id', '=', $supplier_id)
                    ->where('purchase_return_date', '>=', $from_date)
                    ->where('purchase_return_date', '<=', $to_date)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }


            foreach ($purchasesReturns as $purchase) {
                $results[$i]['date'] = date('d-m-Y', $purchase->purchase_return_date);
                $results[$i]['purchase_type'] = 'Purchases Return';
                $results[$i]['supplier'] = CommonHelper::getCustomerName($purchase->supplier_id, 2);
                $results[$i]['total'] = $purchase->total_amout;
                $results[$i]['transport_cost'] = $purchase->transportation_cost;
                $results[$i]['net'] = $purchase->transportation_cost + $purchase->total_amout;
                $results[$i]['paid'] = "";
                $results[$i]['due'] = "";
                $i++;
            }


            $view = view('reports.purchasesReport.report')->with(compact('results'))->render();
            return response()->json($view);

        } elseif ($purchase_type == Config::get('report.purchase_type.Purchases')) {
            $results = [];
            $i = 0;

            if($supplier_id==0)
            {
                $purchases = DB::table('purchases')
                    ->where('purchase_date', '>=', $from_date)
                    ->where('purchase_date', '<=', $to_date)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }else{
                $purchases = DB::table('purchases')
                    ->where('supplier_id', '=', $supplier_id)
                    ->where('purchase_date', '>=', $from_date)
                    ->where('purchase_date', '<=', $to_date)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            foreach ($purchases as $purchase) {
                $results[$i]['date'] = date('d-m-Y', $purchase->purchase_date);
                $results[$i]['purchase_type'] = 'Purchases';
                $results[$i]['supplier'] = CommonHelper::getCustomerName($purchase->supplier_id, 2);
                $results[$i]['total'] = $purchase->total;
                $results[$i]['transport_cost'] = $purchase->transportation_cost;
                $results[$i]['net'] = $purchase->transportation_cost + $purchase->total;
                $results[$i]['paid'] = $purchase->paid;
                $results[$i]['due'] = ($purchase->transportation_cost + $purchase->total) - $purchase->paid;
                $i++;
            }

            $view = view('reports.purchasesReport.report')->with(compact('results'))->render();
            return response()->json($view);
        } elseif ($purchase_type == Config::get('report.purchase_type.Purchases Return')) {
            $results = [];
            $i = 0;

            if($supplier_id==0)
            {
                $purchasesReturns = DB::table('purchases_return')
                    ->where('purchase_return_date', '>=', $from_date)
                    ->where('purchase_return_date', '<=', $to_date)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }else
            {
                $purchasesReturns = DB::table('purchases_return')
                    ->where('supplier_id', '=', $supplier_id)
                    ->where('purchase_return_date', '>=', $from_date)
                    ->where('purchase_return_date', '<=', $to_date)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            foreach ($purchasesReturns as $purchase) {
                $results[$i]['date'] = date('d-m-Y', $purchase->purchase_return_date);
                $results[$i]['purchase_type'] = 'Purchases Return';
                $results[$i]['supplier'] = CommonHelper::getCustomerName($purchase->supplier_id, 2);
                $results[$i]['total'] = $purchase->total_amout;
                $results[$i]['transport_cost'] = $purchase->transportation_cost;
                $results[$i]['net'] = $purchase->transportation_cost + $purchase->total_amout;
                $results[$i]['paid'] = "";
                $results[$i]['due'] = "";
                $i++;
            }

            $view = view('reports.purchasesReport.report')->with(compact('results'))->render();
            return response()->json($view);
        }


    }
}
