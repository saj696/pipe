<?php

namespace App\Http\Controllers\Report;

use App\Models\Workspace;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SalesReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('reportPerm');
    }

    public function index()
    {
        $workspace = Workspace::where('status','=',1)->lists('name','id');
        return view('reports.salesReport.index')->with(compact('workspace'));
    }

    public function getReport(Request $request)
    {
        $this->validate($request, [
            'workspace_id' => 'required',
        ]);

        dd($request->input());
    }

}
