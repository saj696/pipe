<?php

namespace App\Http\Controllers\Register;

use App\Http\Requests;
use App\Models\ProductionRegister;
use App\Models\Product;
use App\Models\Stock;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductionRegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Session;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

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
//        $UsageRegister = UsageRegister::findOrFail($id);
//        return view('usageRegisters.show', compact('UsageRegister'));
    }

    public function create()
    {
        $products = Product::lists('title', 'id');
        return view('productionRegisters.create', compact('products'));
    }

    public function store(ProductionRegisterRequest $request)
    {
        $count = sizeof($request->input('product_id'));
        $productInput = $request->input('product_id');
        $productionInput = $request->input('production');

        DB::beginTransaction();
        try
        {
            for ($i = 0; $i < $count; $i++)
            {
                $productionRegister = New ProductionRegister;
                $productionRegister->date = $request->input('date');
                $productionRegister->product_id = $productInput[$i];
                $productionRegister->production = $productionInput[$i];
                $productionRegister->created_by = Auth::user()->id;
                $productionRegister->created_at = time();
                $productionRegister->save();

                $existingStock = DB::table('stocks')->where(['workspace_id' => 1, 'product_id' => $productInput[$i]])->first();

                if ($existingStock)
                {
                    $stock = Stock::findOrFail($existingStock->id);
                    $stock->quantity = $productionInput[$i] + $existingStock->quantity;
                    $stock->updated_by = Auth::user()->id;
                    $stock->updated_at = time();
                    $stock->update();
                }
                else
                {
                    $stock = New Stock;
                    $stock->workspace_id = 1;
                    $stock->product_id = $productInput[$i];
                    $stock->quantity = $productionInput[$i];
                    $stock->created_by = Auth::user()->id;
                    $stock->created_at = time();
                    $stock->save();
                }
            }

            DB::commit();
            Session()->flash('flash_message', 'Production Register has been created!');
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            Session()->flash('flash_message', 'Production Register not created!');
        }
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
        DB::beginTransaction();
        try
        {
            $existingRegister = DB::table('production_registers')->where('id', $id)->first();
            $UsageRegister = ProductionRegister::findOrFail($id);
            $UsageRegister->date = $request->input('date');
            $UsageRegister->production = $request->input('production');
            $UsageRegister->updated_by = Auth::user()->id;
            $UsageRegister->updated_at = time();
            $UsageRegister->update();

            $existingStock = DB::table('stocks')->where(['workspace_id' => 1, 'product_id' => $existingRegister->product_id])->first();

            if ($existingRegister->production != $request->input('production'))
            {
                if ($existingRegister->production > $request->input('production'))
                {
                    $difference = $existingRegister->production - $request->input('production');
                    $stock = Stock::findOrFail($existingStock->id);
                    $stock->quantity = $existingStock->quantity - $difference;
                    $stock->updated_by = Auth::user()->id;
                    $stock->updated_at = time();
                    $stock->update();
                }
                elseif ($existingRegister->production < $request->input('production'))
                {
                    $difference = $request->input('production') - $existingRegister->production;
                    $stock = Stock::findOrFail($existingStock->id);
                    $stock->quantity = $existingStock->quantity + $difference;
                    $stock->updated_by = Auth::user()->id;
                    $stock->updated_at = time();
                    $stock->update();
                }
            }

            DB::commit();
            Session()->flash('flash_message', 'Production Register has been updated!');
        }
        catch (\Exception $e)
        {
            DB::rollback();
            Session()->flash('flash_message', 'Production Register not updated!');
        }

        return redirect('productionRegisters');
    }
}
