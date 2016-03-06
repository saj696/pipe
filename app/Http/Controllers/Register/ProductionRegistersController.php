<?php

namespace App\Http\Controllers\Register;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\ProductionRegisterRequest;
use App\Models\Product;
use App\Models\ProductionRegister;
use App\Models\Stock;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Session;

class ProductionRegistersController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $productionRegisters = ProductionRegister::paginate(Config::get('common.pagination'));
        $status = Config::get('common.status');
        return view('productionRegisters.index', compact('productionRegisters', 'products', 'status'));
    }

    public function show($id)
    {
//        $productionRegister = UsageRegister::findOrFail($id);
//        return view('usageRegisters.show', compact('UsageRegister'));
    }

    public function create()
    {
        $products = Product::lists('title', 'id');
        return view('productionRegisters.create', compact('products'));
    }

    public function store(ProductionRegisterRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $currentYear = CommonHelper::get_current_financial_year();
                $workspace_id = Auth::user()->workspace_id;
                $count = sizeof($request->input('product_id'));
                $productInput = $request->input('product_id');
                $productionInput = $request->input('production');

                for ($i = 0; $i < $count; $i++) {
                    $productionRegister = New ProductionRegister;
                    $productionRegister->date = $request->input('date');
                    $productionRegister->year = $currentYear;
                    $productionRegister->product_id = $productInput[$i];
                    $productionRegister->production = $productionInput[$i];
                    $productionRegister->created_by = Auth::user()->id;
                    $productionRegister->created_at = time();
                    $productionRegister->save();

                    $existingStock = DB::table('stocks')->where(['stock_type' => Config::get('common.balance_type_intermediate'), 'year' => $currentYear, 'workspace_id' => 1, 'product_id' => $productInput[$i]])->first();

                    if ($existingStock) {
                        $stock = Stock::findOrFail($existingStock->id);
                        $stock->quantity = $productionInput[$i] + $existingStock->quantity;
                        $stock->updated_by = Auth::user()->id;
                        $stock->updated_at = time();
                        $stock->update();
                    } else {
                        // Opening Stock Entry
                        $stock = New Stock;
                        $stock->year = $currentYear;
                        $stock->stock_type = Config::get('common.balance_type_opening');
                        $stock->workspace_id = $workspace_id;
                        $stock->product_id = $productInput[$i];
                        $stock->quantity = $productionInput[$i];
                        $stock->created_by = Auth::user()->id;
                        $stock->created_at = time();
                        $stock->save();
                        // Intermediate Stock Entry
                        $stock = New Stock;
                        $stock->year = $currentYear;
                        $stock->stock_type = Config::get('common.balance_type_intermediate');
                        $stock->workspace_id = $workspace_id;
                        $stock->product_id = $productInput[$i];
                        $stock->quantity = $productionInput[$i];
                        $stock->created_by = Auth::user()->id;
                        $stock->created_at = time();
                        $stock->save();
                    }
                }
            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Production Register not created!');
            return redirect('productionRegisters');
        }

        Session()->flash('flash_message', 'Production Register has been created!');
        return redirect('productionRegisters');
    }

    public function edit($id)
    {
        $productionRegister = ProductionRegister::with('product')->findOrFail($id);
        $products = Product::lists('title', 'id');
        return view('productionRegisters.edit', compact('productionRegister', 'products'));
    }

    public function update($id, ProductionRegisterRequest $request)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $user = Auth::user();
                $existingRegister = DB::table('production_registers')->where('id', $id)->first();
                $productionRegister = ProductionRegister::findOrFail($id);
                $productionRegister->date = $request->input('date');
                $productionRegister->production = $request->input('production');
                $productionRegister->updated_by = Auth::user()->id;
                $productionRegister->updated_at = time();
                $productionRegister->update();

                $existingStock = DB::table('stocks')->where(['year' => CommonHelper::get_current_financial_year(), 'stock_type' => Config::get('common.balance_type_intermediate'), 'workspace_id' => $user->workspace_id, 'product_id' => $existingRegister->product_id])->first();

                if ($existingRegister->production != $request->input('production')) {
                    if ($existingRegister->production > $request->input('production')) {
                        $difference = $existingRegister->production - $request->input('production');
                        $stock = Stock::findOrFail($existingStock->id);
                        $stock->quantity = $existingStock->quantity - $difference;
                        $stock->updated_by = Auth::user()->id;
                        $stock->updated_at = time();
                        $stock->update();
                    } elseif ($existingRegister->production < $request->input('production')) {
                        $difference = $request->input('production') - $existingRegister->production;
                        $stock = Stock::findOrFail($existingStock->id);
                        $stock->quantity = $existingStock->quantity + $difference;
                        $stock->updated_by = Auth::user()->id;
                        $stock->updated_at = time();
                        $stock->update();
                    }
                }
            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Production Register not updated!');
            return redirect('productionRegisters');
        }

        Session()->flash('flash_message', 'Production Register has been updated!');
        return redirect('productionRegisters');
    }
}
