<?php

namespace App\Http\Controllers\Setup;

use App\Http\Requests\PurchaseRequest;
use App\Models\Material;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\RawStock;
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
        $suppliers = Supplier::where('status',1)->lists('company_name','id');
        $rmaterials = Material::where('status',1)->select('name','id','type')->get();
        $materials =[];
        foreach($rmaterials as $material)
        {
            if($material->type != 1)
                $materials[$material->id] = Config::get('common.material_type')[$material->type].' - '.$material->name;
            else
                $materials[$material->id] = $material->name;
        }
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
        if(!$request->input('items')){
            Session()->flash('error_message','Purchases Update has not been Completed');
            return redirect('purchases');
        }
        $user_id = Auth::user()->id;
        $time = time();
        $purchase = New Purchase();
        $purchase->supplier_id = $request->input('supplier_id');
        $purchase->purchase_date = $request->input('purchase_date');
        $purchase->transportation_cost = $request->input('transportation_cost');
        $purchase->paid = $request->input('paid');
        $purchase->total = $request->input('total');
        $purchase->created_at = time();
        $purchase->created_by = $user_id;
        $purchase->save();
        $purchase_id = $purchase->id;
        foreach($request->input('items') as $item){
            //purchase details
            $item['purchase_id'] = $purchase_id;
            $item['status'] = 1;
            $item['created_at'] = time();
            $item['created_by'] = $user_id;
            PurchaseDetail::create($item);
            //update stock info
            RawStock::where('material_id',$item['material_id'])->increment('quantity',$item['quantity'],['updated_at'=>$time,'updated_by'=>$user_id]);
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
        $purchase = Purchase::with('purchaseDetails.material','supplier')->findOrFail($id);
//        dd($purchase);
        return view('purchases.show',compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $suppliers = Supplier::where('status',1)->lists('company_name','id');
        $materials = Material::where('status',1)->lists('name','id');
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
        if(!$request->input('items')){
            Session()->flash('error_message','Purchases Update has not been Completed');
            return redirect('purchases');
        }
        $time = time();
        $user_id = Auth::user()->id;
        $purchase = Purchase::findOrFail($id);
        $purchase->supplier_id = $request->input('supplier_id');
        $purchase->purchase_date = $request->input('purchase_date');
        $purchase->transportation_cost = $request->input('transportation_cost');
        $purchase->paid = $request->input('paid');
        $purchase->total = $request->input('total');
        $purchase->updated_at = time();
        $purchase->updated_by = $user_id;
        $purchase->update();
        //get all old items
        $arrange_old_items = [];
        $old_purchases = PurchaseDetail::where('purchase_id',$id)->get();
        foreach($old_purchases as $old_purchase){
            $arrange_old_items[$old_purchase['material_id']]= $old_purchase;
        }
        foreach($request->input('items') as $item){
            if(isset($arrange_old_items[$item['material_id']]))//if old data
            {
                // update old data
                $PurchaseDetail = PurchaseDetail::findOrFail($arrange_old_items[$item['material_id']]['id']);
                $PurchaseDetail->quantity = $item['quantity'];
                $PurchaseDetail->received_quantity = $item['received_quantity'];
                $PurchaseDetail->unit_price = $item['unit_price'];
                $PurchaseDetail->status = 1;
                $PurchaseDetail->updated_at = time();
                $PurchaseDetail->updated_by = $user_id;
                $PurchaseDetail->update();
                //update stock info
                if($arrange_old_items[$item['material_id']]['received_quantity'] < $item['received_quantity']){
                    $add_amount = $item['received_quantity']-$arrange_old_items[$item['material_id']]['received_quantity'];
                    RawStock::where('material_id',$item['material_id'])
                        ->increment('quantity',$add_amount,['updated_at'=>$time,'updated_by'=>$user_id]);
                }
                elseif($arrange_old_items[$item['material_id']]['received_quantity'] > $item['received_quantity']){
                    $sub_amount = $arrange_old_items[$item['material_id']]['received_quantity']-$item['received_quantity'];
                    RawStock::where('material_id',$item['material_id'])
                        ->decrement('quantity',$sub_amount,['updated_at'=>$time,'updated_by'=>$user_id]);
                }
                unset($arrange_old_items[$item['material_id']]);
            }
            else//if new data
            {
                //purchase details
                $item['purchase_id'] = $id;
                $item['status'] = 1;
                $item['created_at'] = time();
                $item['created_by'] = $user_id;
                PurchaseDetail::create($item);
                //update stock info
                RawStock::where('material_id',$item['material_id'])
                    ->increment('quantity',$item['received_quantity'],['updated_at'=>$time,'updated_by'=>$user_id]);
            }
        }
        //delete old data
        foreach($arrange_old_items as $old_item)
        {
            //reduce the stock info
            RawStock::where('material_id',$old_item['material_id'])
                ->decrement('quantity',$old_item['received_quantity'],['updated_at'=>$time,'updated_by'=>$user_id]);
            //update the purchase info
            $PurchaseDetail = PurchaseDetail::findOrFail($old_item['id']);
            $PurchaseDetail->delete();
        }
        Session()->flash('flash_message','Purchases Update has been Completed');
        return redirect('purchases');
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
