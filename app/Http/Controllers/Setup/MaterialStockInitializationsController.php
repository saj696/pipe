<?php

namespace App\Http\Controllers\Setup;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\MaterialStockInitializationsRequest;
use App\Models\Material;
use App\Models\RawStock;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Session;

class MaterialStockInitializationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $materials = Material::paginate(Config::get('common.pagination'));
        $types = Config::get('common.material_type');
        $status = Config::get('common.status');
        return view('materialStockInitializations.index', compact('materials', 'types', 'status'));
    }

    public function edit($id)
    {
        $types = Config::get('common.material_type');
        $material = Material::findOrFail($id);
        return view('materialStockInitializations.edit', compact('types', 'material'));
    }

    public function update($id, MaterialStockInitializationsRequest $request)
    {
        $year = CommonHelper::get_current_financial_year();
        $stock = New RawStock();

        $stock::where(['material_id' => $id, 'stock_type' => Config::get('common.balance_type_opening'), 'year' => $year])
            ->increment('quantity', $request->input('opening_stock'), ['updated_at' => time(), 'updated_by' => Auth::user()->id]);

        $stock::where(['material_id' => $id, 'stock_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])
            ->increment('quantity', $request->input('opening_stock'), ['updated_at' => time(), 'updated_by' => Auth::user()->id]);

        Session()->flash('flash_message', 'Material Stock has been initialized!');
        return redirect('material_stock_initializations');
    }
}
