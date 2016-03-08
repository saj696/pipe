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

class MaterialUsageReportController extends Controller
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
        return view('reports.materialUsage.index')->with(compact('workspace'));
    }

    public function getReport(Request $request)
    {
        $this->validate($request, [
            'month' => 'required',
        ]);

        $month = $request->month;
        $from_date = strtotime('01-'.$month.'-'.CommonHelper::get_current_financial_year());
        $to_date = strtotime(date('t-m-Y', $from_date));

        $usages = DB::table('usage_registers')
            ->select('usage_registers.*', 'materials.name')
            ->join('materials', 'materials.id', '=', 'usage_registers.material_id')
            ->where('usage_registers.created_at', '>=', $from_date)
            ->where('usage_registers.created_at', '<=', $to_date)
            ->get();

        $arrangedArray = [];

        foreach($usages as $usage)
        {
            $dates[] = date('d.m.Y', $usage->date);
            $materials[] = $usage->name;
        }

        $uniqueDates = collect($dates)->unique()->values()->all();
        $uniqueMaterial = collect($materials)->unique()->values()->all();

        foreach($usages as $usage)
        {
            $arrangedArray[date('d.m.Y', $usage->date)][$usage->name] = $usage->usage;
        }

        $ajaxView = view('reports.materialUsage.view', compact('uniqueDates', 'uniqueMaterial', 'arrangedArray'))->render();
        return response()->json($ajaxView);
    }

}
