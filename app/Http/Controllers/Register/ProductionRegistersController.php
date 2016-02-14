<?php

namespace App\Http\Controllers\Register;

use App\Http\Requests;
use App\Models\ProductionRegister;
use App\Models\Product;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductionRegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Session;
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

        for($i=0; $i<$count; $i++)
        {
            $productionRegister = New ProductionRegister;
            $productionRegister->date = $request->input('date');
            $productionRegister->product_id = $productInput[$i];
            $productionRegister->production = $productionInput[$i];
            $productionRegister->created_by = Auth::user()->id;
            $productionRegister->created_at = time();

            $productionRegister->save();
        }

        Session()->flash('flash_message', 'Production Register has been created!');
        return redirect('productionRegisters');
    }

    public function edit($id)
    {
        $productionRegister = ProductionRegister::findOrFail($id);
        $products = Product::lists('name', 'id');
        return view('productionRegisters.edit', compact('productionRegister', 'products'));
    }

    public function update($id, UsageRegisterRequest $request)
    {
        $UsageRegister = UsageRegister::findOrFail($id);

        $UsageRegister->date = $request->input('date');
        $UsageRegister->product_id = $request->input('product_id');
        $UsageRegister->usage = $request->input('usage');
        $UsageRegister->status = $request->input('status');
        $UsageRegister->updated_by = Auth::user()->id;
        $UsageRegister->updated_at = time();
        $UsageRegister->update();

        Session()->flash('flash_message', 'Usage Register has been updated!');
        return redirect('usageRegisters');
    }
}
