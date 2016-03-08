<?php

namespace App\Http\Controllers\Report;

use App\Models\Workspace;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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

        dd($request->input());
    }

}
