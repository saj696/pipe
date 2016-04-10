<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\FinancialYear;
use App\Models\Workspace;
use DB;
use Illuminate\Http\Request;

class PayrollReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('reportPerm');
    }

    public function index()
    {
        $workspaces = Workspace::lists('name', 'id');
        $years = FinancialYear::lists('year', 'year');
        return view('reports.payrollReport.index')->with(compact('years', 'workspaces'));
    }

    public function getReport(Request $request)
    {
        $this->validate($request, [
            'month' => 'required',
            'year' => 'required',
        ]);

        if ($request->workspace_id == 0) {

            $salaries = DB::table('salaries')
                ->select('salaries.*', 'employees.name as employee_name', 'workspaces.name as workspace_name')
                ->leftJoin('employees', 'salaries.employee_id', '=', 'employees.id')
                ->leftJoin('workspaces', 'salaries.workspace_id', '=', 'workspaces.id')
                ->where('month', '=', $request->month)
                ->where('year', '=', $request->year)
                ->get();
//            dd($salaries);
            $ajaxView = view('reports.payrollReport.report')->with(compact('salaries'))->render();

            return response()->json($ajaxView);
        } else {
            $salaries = DB::table('salaries')
                ->select('salaries.*', 'employees.name as employee_name', 'workspaces.name as workspace_name')
                ->join('employees', 'employees.id', '=', 'salaries.employee_id')
                ->leftJoin('workspaces', 'salaries.workspace_id', '=', 'workspaces.id')
                ->where('salaries.month', '=', $request->month)
                ->where('salaries.workspace_id', '=', $request->workspace_id)
                ->where('salaries.year', '=', $request->year)
                ->get();

            $ajaxView = view('reports.payrollReport.report')->with(compact('salaries'))->render();

            return response()->json($ajaxView);
        }


    }
}
