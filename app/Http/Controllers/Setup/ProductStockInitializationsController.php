<?php

namespace App\Http\Controllers\Setup;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\ProductStockInitializationsRequest;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Stock;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Session;

class ProductStockInitializationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $products = Product::paginate(Config::get('common.pagination'));
        $types = ProductType::where('status', 1)->lists('title', 'id');
        return view('productStockInitializations.index', compact('products', 'types'));
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('productStockInitializations.edit', compact('product'));
    }

    public function update($id, ProductStockInitializationsRequest $request)
    {
        $year = CommonHelper::get_current_financial_year();
        $stock = New Stock();

        $stock::where(['product_id' => $id, 'stock_type' => Config::get('common.balance_type_opening'), 'year' => $year])
            ->increment('quantity', $request->input('opening_stock'), ['updated_at' => time(), 'updated_by' => Auth::user()->id]);

        $stock::where(['product_id' => $id, 'stock_type' => Config::get('common.balance_type_intermediate'), 'year' => $year])
            ->increment('quantity', $request->input('opening_stock'), ['updated_at' => time(), 'updated_by' => Auth::user()->id]);

        Session()->flash('flash_message', 'Product Stock has been initialized!');
        return redirect('product_stock_initializations');
    }
}
