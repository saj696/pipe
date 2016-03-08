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

class TrialBalanceController extends Controller
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
        return view('reports.trialBalance.index')->with(compact('workspace'));
    }

    public function getReport(Request $request)
    {
        $this->validate($request, [
            'workspace_id' => 'required',
        ]);

        $workspace_id = $request->workspace_id;

        $debits = DB::table('general_journals')
            ->select('general_journals.*', 'chart_of_accounts.name')
            ->join('chart_of_accounts', 'chart_of_accounts.code', '=', 'general_journals.account_code')
            ->where(['workspace_id'=>$workspace_id, 'dr_cr_indicator'=>Config::get('common.debit_credit_indicator.debit'), 'year'=>CommonHelper::get_current_financial_year(), 'general_journals.status'=>1])
            ->get();
        $credits = DB::table('general_journals')
            ->select('general_journals.*', 'chart_of_accounts.name')
            ->join('chart_of_accounts', 'chart_of_accounts.code', '=', 'general_journals.account_code')
            ->where(['workspace_id'=>$workspace_id, 'dr_cr_indicator'=>Config::get('common.debit_credit_indicator.credit'), 'year'=>CommonHelper::get_current_financial_year(), 'general_journals.status'=>1])
            ->get();

        $ajaxView = view('reports.trialBalance.view', compact('debits', 'credits'))->render();
        return response()->json($ajaxView);
    }

}
