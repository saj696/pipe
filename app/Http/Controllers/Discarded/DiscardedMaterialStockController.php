<?php

namespace App\Http\Controllers\Discarded;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\DiscardedStockRequest;
use App\Http\Requests\Request;
use App\Models\Material;
use App\Models\RawStock;
use App\Models\UsageRegister;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Session;

class DiscardedMaterialStockController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
//        $discardedMaterials = Material::where('type', array_flip(Config::get('common.material_type'))['Discarded'])->lists('id');
//        $discardedMaterialStocks = RawStock::whereIn('id', $discardedMaterials)->paginate(Config::get('common.pagination'));
//
//        $materials = Material::lists('name', 'id');
//        return view('discardedStock.index', compact('discardedMaterialStocks', 'materials'));
        $materials = Material::where('type', array_flip(Config::get('common.material_type'))['Discarded'])->lists('name','id');
        return view('discardedStock.create', compact('materials'));
    }

//    public function create()
//    {
//        $materials = Material::where('type', array_flip(Config::get('common.material_type'))['Discarded'])->lists('name','id');
//        return view('discardedStock.create', compact('materials'));
//    }

    public function store(DiscardedStockRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $materialInput = $request->input('material_id');
                $quantityInput = $request->input('quantity');

                $rawStock = RawStock::where(['material_id' => $materialInput, 'year' => CommonHelper::get_current_financial_year(), 'stock_type' => Config::get('common.balance_type_intermediate')])->first();

                $rawStock->quantity += $quantityInput;
                $rawStock->updated_by = Auth::user()->id;
                $rawStock->updated_at = time();
                $rawStock->update();
            });
        } catch (\Exception $e) {
            Session()->flash('warning_message', 'Discarded Material Entry Not Done!');
            return redirect('discarded_stock');
        }

        Session()->flash('flash_message', 'Discarded Material Entry Successful!');
        return redirect('discarded_stock');
    }
}
