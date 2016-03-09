<?php

namespace App\Http\Controllers\Report;

use App\Helpers\CommonHelper;
use App\Models\GeneralJournal;
use App\Models\Workspace;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ProductionRegisterReportController extends Controller
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
        return view('reports.productionRegister.index')->with(compact('workspace'));
    }

    public function getReport(Request $request)
    {
        $this->validate($request, [
            'month' => 'required',
        ]);

        $month = $request->month;
        $from_date = strtotime('01-'.$month.'-'.CommonHelper::get_current_financial_year());
        $to_date = strtotime(date('t-m-Y', $from_date));

        $registers = DB::table('production_registers')
            ->select('production_registers.*', 'products.title')
            ->join('products', 'products.id', '=', 'production_registers.product_id')
            ->where('production_registers.date', '>=', $from_date)
            ->where('production_registers.date', '<=', $to_date)
            ->get();

        $arrangedArray = [];

        $dates = [];
        $products = [];

        foreach($registers as $register)
        {
            $dates[] = date('d.m.Y', $register->date);
            $products[] = $register->title;
        }

        $uniqueDates = collect($dates)->unique()->values()->all();
        $uniqueProducts = collect($products)->unique()->values()->all();

        foreach($registers as $register)
        {
            $arrangedArray[date('d.m.Y', $register->date)][$register->title] = $register->production;
        }

        $ajaxView = view('reports.productionRegister.view', compact('uniqueDates', 'uniqueProducts', 'arrangedArray'))->render();
        return response()->json($ajaxView);
    }

}
