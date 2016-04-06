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

class IncomeStatementReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('reportPerm');
    }

    public function index()
    {
        $workspace_id = Auth::user()->workspace_id;
        if ($workspace_id == 1) {
            $workspaces = Workspace::where('status', '=', 1)->lists('name', 'id');
        } else {
            $workspaces = Workspace::where(['id' => $workspace_id])->lists('name', 'id');
        }
        return view('reports.incomeStatement.index', compact('workspaces'));
    }

    public function getReport(Request $request)
    {
        $this->validate($request, [
            'ending_date' => 'required',
        ]);

        $workspace_id = $request->workspace;
        $ending_date = strtotime($request->ending_date);

        if($workspace_id>0)
        {
            $revenues = DB::table('general_journals')
                ->select('general_journals.*', DB::raw('SUM(amount) as sum_amount'), 'chart_of_accounts.name', 'chart_of_accounts.contra_status')
                ->join('chart_of_accounts', 'chart_of_accounts.code', '=', 'general_journals.account_code')
                ->where(['workspace_id'=>$workspace_id, 'year'=>CommonHelper::get_current_financial_year(), 'general_journals.status'=>1])
                ->where('general_journals.account_code', 'like', '3%')
                ->where('general_journals.date', '<=', $ending_date)
                ->groupBy('general_journals.account_code')
                ->get();

            $expenses = DB::table('general_journals')
                ->select('general_journals.*', DB::raw('SUM(amount) as sum_amount'), 'chart_of_accounts.name', 'chart_of_accounts.contra_status')
                ->join('chart_of_accounts', 'chart_of_accounts.code', '=', 'general_journals.account_code')
                ->where(['workspace_id'=>$workspace_id, 'year'=>CommonHelper::get_current_financial_year(), 'general_journals.status'=>1])
                ->where('general_journals.account_code', 'like', '2%')
                ->where('general_journals.date', '<=', $ending_date)
                ->groupBy('general_journals.account_code')
                ->get();
        }
        else
        {
            $revenues = DB::table('general_journals')
                ->select('general_journals.*', DB::raw('SUM(amount) as sum_amount'), 'chart_of_accounts.name', 'chart_of_accounts.contra_status')
                ->join('chart_of_accounts', 'chart_of_accounts.code', '=', 'general_journals.account_code')
                ->where(['year'=>CommonHelper::get_current_financial_year(), 'general_journals.status'=>1])
                ->where('general_journals.account_code', 'like', '3%')
                ->where('general_journals.date', '<=', $ending_date)
                ->groupBy('general_journals.account_code')
                ->get();

            $expenses = DB::table('general_journals')
                ->select('general_journals.*', DB::raw('SUM(amount) as sum_amount'), 'chart_of_accounts.name', 'chart_of_accounts.contra_status')
                ->join('chart_of_accounts', 'chart_of_accounts.code', '=', 'general_journals.account_code')
                ->where(['year'=>CommonHelper::get_current_financial_year(), 'general_journals.status'=>1])
                ->where('general_journals.account_code', 'like', '2%')
                ->where('general_journals.date', '<=', $ending_date)
                ->groupBy('general_journals.account_code')
                ->get();
        }

        $ajaxView = view('reports.incomeStatement.view', compact('revenues', 'expenses', 'ending_date'))->render();
        return response()->json($ajaxView);
    }
}
