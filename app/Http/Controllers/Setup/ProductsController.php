<?php

namespace App\Http\Controllers\Setup;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\Material;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with('productTypes','materials')->paginate(Config::get('common.pagination'));
        return view('products/index')->with('products',$products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product_types = ProductType::lists('title','id');
        $color = Material::where('type',3)->lists('name','id');

        return view('products/create',compact('product_types','color'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $product = New Product();
        $product->title = $request->input('title');
        $product->product_type_id = $request->input('product_type_id');
        $product->length = $request->input('length');
        $product->diameter = $request->input('diameter');
        $product->weight = $weight = $request->input('weight');
        if(!is_numeric($weight))
        {
            $nmub = explode('/',$weight);
            $product->weight = $nmub[1]/$nmub[0];
        }
        $product->color = $request->input('color');
        $product->wholesale_price = $request->input('wholesale_price');
        $product->retail_price = $request->input('retail_price');
        $product->status = $request->input('status');
        $product->created_at = time();
        $product->created_by = Auth::user()->id;
        $product->save();
        Session()->flash('flash_message', 'Data has been Saved');
        return redirect('products');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function show($id)
//    {
//        //
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $product_types = ProductType::lists('title','id');
        $color = Material::where('type',3)->lists('name','id');
        return view('products.edit',compact('product','product_types','color'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
//        dd($request->input());
        $product = Product::findOrFail($id);
        $product->title = $request->input('title');
        $product->product_type_id = $request->input('product_type_id');
        $product->length = $request->input('length');
        $product->diameter = $request->input('diameter');
        $product->weight = $weight = $request->input('weight');
        if(!is_numeric($weight))
        {
            $nmub = explode('/',$weight);
            $product->weight = $nmub[1]/$nmub[0];
        }
        $product->color = $request->input('color');
        $product->wholesale_price = $request->input('wholesale_price');
        $product->retail_price = $request->input('retail_price');
        $product->status = $request->input('status');
        $product->update_at = time();
        $product->update_by = Auth::user()->id;
        $product->update();
        Session()->flash('flash_message', 'Data has been Updated');
        return redirect('products');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function destroy($id)
//    {
//        //
//    }
}
