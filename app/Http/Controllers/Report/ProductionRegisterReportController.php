<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\FinancialYear;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionRegisterReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('reportPerm');
    }

    public function index()
    {
        $years = FinancialYear::lists('year', 'year');
        $workspace_id = Auth::user()->workspace_id;
        if ($workspace_id == 1) {
            $workspace = Workspace::where('status', '=', 1)->lists('name', 'id');
        } else {
            $workspace = Workspace::where(['id' => $workspace_id])->lists('name', 'id');
        }
        return view('reports.productionRegister.index')->with(compact('workspace', 'years'));
    }

    public function getReport(Request $request)
    {
        $this->validate($request, [
            'year' => 'required',
            'month' => 'required',
        ]);

        $month = $request->month;
        $from_date = strtotime('01-' . $month . '-' . $request->year);
        $to_date = strtotime(date('t-m-Y', $from_date));

        $start_year_date = strtotime('01-01-' . $request->year);
        $end_year_date = strtotime('31-12-' . $request->year);

        $registers = DB::table('production_registers')
            ->select('production_registers.*', 'products.title')
            ->join('products', 'products.id', '=', 'production_registers.product_id')
            ->where('production_registers.date', '>=', $start_year_date)
            ->where('production_registers.date', '<=', $end_year_date)
            ->where('production_registers.date', '>=', $from_date)
            ->where('production_registers.date', '<=', $to_date)
            ->get();

        $arrangedArray = [];

        $dates = [];
        $products = [];

        foreach ($registers as $register) {
            $dates[] = date('d.m.Y', $register->date);
            $products[] = $register->title;
        }

        $uniqueDates = collect($dates)->unique()->values()->all();
        $uniqueProducts = collect($products)->unique()->values()->all();

        foreach ($registers as $register) {
            $arrangedArray[date('d.m.Y', $register->date)][$register->title] = $register->production;
        }

        $ajaxView = view('reports.productionRegister.view', compact('uniqueDates', 'uniqueProducts', 'arrangedArray'))->render();
        return response()->json($ajaxView);
    }

}
