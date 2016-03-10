<?php

namespace App\Http\Controllers\Report;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class StockReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('reportPerm');
    }

    public function index()
    {
        $workspace_id = Auth::user()->workspace_id;
        if ($workspace_id == 1) {
            $workspace = Workspace::where('status', '=', 1)->lists('name', 'id');
        } else {
            $workspace = Workspace::where(['id' => $workspace_id])->lists('name', 'id');
        }

        return view('reports.stocks.index')->with(compact('workspace'));
    }

    public function getReport(Request $request)
    {
        $this->validate($request, [
            'stock_type' => 'required',
            'workspace_id' => 'required_if:stock_type,2'
        ],
            [
                'workspace_id.required_if'=>'The workspace field is required.'
            ]

        );

        $stock_type = $request->stock_type;
        $workspace_id = $request->workspace_id;
        $currentYear = CommonHelper::get_current_financial_year();

        if ($stock_type == 1) {
            $stocks = DB::table('raw_stocks')
                ->select('raw_stocks.*', 'materials.name')
                ->where(['stock_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear])
                ->join('materials', 'materials.id', '=', 'raw_stocks.material_id')
                ->get();
        } elseif ($stock_type == 2) {
            $stocks = DB::table('stocks')
                ->select('stocks.*', 'products.title')
                ->where(['stock_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear, 'workspace_id' => $workspace_id])
                ->join('products', 'products.id', '=', 'stocks.product_id')
                ->get();
        }

        $ajaxView = view('reports.stocks.view', compact('stocks', 'stock_type'))->render();
        return response()->json($ajaxView);
    }

}
