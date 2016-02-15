<?php

namespace App\Http\Controllers\Setup;

use App\Http\Requests\PurchaseRequest;
use App\Models\Material;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class PurchasesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $purchases = Purchase::orderBy('id','DESC')->with('supplier','purchaseDetails')->paginate(Config::get('common.pagination'));
        return view('purchases.index',compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::lists('company_name','id');
        $materials = Material::lists('name','id');
        return view('purchases.create',compact('suppliers','materials'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PurchaseRequest $request)
    {
//        dd($request->input());
        $user_id = Auth::user()->id;
        $purchase = New Purchase();
        $purchase->supplier_id = $request->input('supplier_id');
        $purchase->purchase_date = $request->input('purchase_date');
        $purchase->transportation_cost = $request->input('transportation_cost');
        $purchase->paid = $request->input('paid');
        $purchase->total = $request->input('total');
        $purchase->save();
        $purchase_id = $purchase->id;
        foreach($request->input('items') as $item){
            $item['purchase_id'] = $purchase_id;
            $item['status'] = 1;
            $item['created_at'] = time();
            $item['created_by'] = $user_id;
            PurchaseDetail::create($item);
        }
        Session()->flash('flash_message','Purchases has been Completed');
        return redirect('purchases');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $suppliers = Supplier::lists('company_name','id');
        $materials = Material::lists('name','id');
        $purchase = Purchase::with('purchaseDetails')->findOrFail($id);
//        $purchase = Purchase::findOrFail($id);
//        dd($purchase);
        return view('purchases.edit',compact('suppliers','materials','purchase'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
