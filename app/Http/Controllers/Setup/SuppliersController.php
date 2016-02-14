<?php

namespace App\Http\Controllers\Setup;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class SuppliersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = Supplier::paginate(Config::get('common.pagination'));
        return view('suppliers.index',compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierRequest $request)
    {
//        dd($request->input());
        $supplier = New Supplier();
        $supplier->suppliers_type = $request->input('suppliers_type');
        $supplier->company_name = $request->input('company_name');
        $supplier->company_address = $request->input('company_address');
        $supplier->company_office_phone = $request->input('company_office_phone');
        $supplier->company_office_fax = $request->input('company_office_fax');
        $supplier->contact_person = $request->input('contact_person');
        $supplier->contact_person_phone = $request->input('contact_person_phone');
        $supplier->supplier_description = $request->input('supplier_description');
        $supplier->status = $request->input('status');
        $supplier->created_at = time();
        $supplier->created_by = Auth::user()->id;
        $supplier->save();
        Session()->flash('flash_message','Data has been Saved');
        return redirect('suppliers');
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
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.edit')->with('supplier',$supplier);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SupplierRequest $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->suppliers_type = $request->input('suppliers_type');
        $supplier->company_name = $request->input('company_name');
        $supplier->company_address = $request->input('company_address');
        $supplier->company_office_phone = $request->input('company_office_phone');
        $supplier->company_office_fax = $request->input('company_office_fax');
        $supplier->contact_person = $request->input('contact_person');
        $supplier->contact_person_phone = $request->input('contact_person_phone');
        $supplier->supplier_description = $request->input('supplier_description');
        $supplier->status = $request->input('status');
        $supplier->updated_at = time();
        $supplier->updated_by = Auth::user()->id;
        $supplier->update();
        Session()->flash('flash_message','Data has been Updated');
        return redirect('suppliers');
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
