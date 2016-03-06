<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\ProductTypeRequest;
use App\Models\ProductType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class ProductTypesController extends Controller
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
        return view('productTypes/index')->with('product_types', ProductType::paginate(Config::get('common.pagination')));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('productTypes/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductTypeRequest $request)
    {

        $product_type = New ProductType;
        $product_type->title = $request->input(['title']);
        $product_type->status = 1;
        $product_type->created_at = time();
        $product_type->created_by = Auth::user()->id;
        $product_type->save();
        Session()->flash('flash_message', 'Data has been Saved');
        return redirect('product_types');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
//    public function show($id)
//    {
//        //
//    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product_type = ProductType::findOrFail($id);
        return view('productTypes.edit')->with('product_type', $product_type);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductTypeRequest $request, $id)
    {
        $product_type = ProductType::findOrFail($id);
        $product_type->title = $request->input('title');
        $product_type->status = $request->input('status');
        $product_type->updated_at = time();
        $product_type->updated_by = Auth::user()->id;
        $product_type->update();
        Session()->flash('flash_message', 'Data has been Updated');
        return redirect('product_types');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
//    public function destroy($id)
//    {
//        //
//    }
}
