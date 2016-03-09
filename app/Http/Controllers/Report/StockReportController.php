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

class StockReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('reportPerm');
    }

    public function index()
    {
        return view('reports.stocks.index');
    }

    public function getReport(Request $request)
    {
        $this->validate($request, [
            'stock_type' => 'required',
        ]);

        $stock_type = $request->stock_type;

        if($stock_type==1)
        {
            $stocks = DB::table('raw_stocks')
                ->select('raw_stocks.*', 'products.title')
                ->join('materials', 'materials.id', '=', 'raw_stocks.material_id')
                ->get();
        }
        elseif($stock_type==2)
        {
            $stocks = DB::table('production_registers')
                ->select('production_registers.*', 'products.title')
                ->join('products', 'products.id', '=', 'production_registers.product_id')
                ->get();
        }

        $ajaxView = view('reports.stocks.view', compact('stocks'))->render();
        return response()->json($ajaxView);
    }

}
