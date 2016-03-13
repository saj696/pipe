<?php

namespace App\Http\Controllers\Report;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\FinancialYear;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterialUsageReportController extends Controller
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
        return view('reports.materialUsage.index')->with(compact('workspace', 'years'));
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

        $usages = DB::table('usage_registers')
            ->select('usage_registers.*', 'materials.name')
            ->join('materials', 'materials.id', '=', 'usage_registers.material_id')
            /*->where('usage_registers.date', '>=', $start_year_date)
            ->where('usage_registers.date', '<=', $end_year_date)*/
            ->where('usage_registers.date', '>=', $from_date)
            ->where('usage_registers.date', '<=', $to_date)
            ->get();

        $arrangedArray = [];

        $dates = [];
        $materials = [];

        foreach ($usages as $usage) {
            $dates[] = date('d.m.Y', $usage->date);
            $materials[] = $usage->name;
        }

        $uniqueDates = collect($dates)->unique()->values()->all();
        $uniqueMaterials = collect($materials)->unique()->values()->all();

        foreach ($usages as $usage) {
            $arrangedArray[date('d.m.Y', $usage->date)][$usage->name] = $usage->usage;
        }

        $ajaxView = view('reports.materialUsage.view', compact('uniqueDates', 'uniqueMaterials', 'arrangedArray'))->render();
        return response()->json($ajaxView);
    }

}
