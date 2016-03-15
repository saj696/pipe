<?php

namespace App\Http\Controllers\Discarded;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\DiscardedSalesRequest;
use App\Models\DiscardedSales;
use App\Models\Material;
use App\Models\RawStock;
use App\Models\UsageRegister;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Session;

class DiscardedMaterialSaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $discardedSales = DiscardedSales::with('material')->paginate(Config::get('common.pagination'));
        return view('discardedSales.index', compact('discardedSales'));
    }

    public function create()
    {
        $materials = Material::where('type', array_flip(Config::get('common.material_type'))['Discarded'])->lists('name','id');
        return view('discardedSales.create', compact('materials'));
    }

    public function store(DiscardedSalesRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $count = sizeof($request->input('material_id'));
                $materialInput = $request->input('material_id');
                $usageInput = $request->input('usage');

                for ($i = 0; $i < $count; $i++) {
                    $rawStock = RawStock::where(['material_id' => $materialInput[$i], 'year' => CommonHelper::get_current_financial_year(), 'stock_type' => Config::get('common.balance_type_intermediate')])->first();

                    if ($rawStock->quantity >= $usageInput[$i]) {

                        $today = UsageRegister::where(['date'=>strtotime($request->input('date')), 'material_id'=>$materialInput[$i]])->first();
                        if($today)
                        {
                            $today->usage += $usageInput[$i];
                            $today->updated_by = Auth::user()->id;
                            $today->updated_by = time();
                            $today->update();
                        }
                        else
                        {
                            $UsageRegister = New UsageRegister;
                            $UsageRegister->date = $request->input('date');
                            $UsageRegister->material_id = $materialInput[$i];
                            $UsageRegister->usage = $usageInput[$i];
                            $UsageRegister->created_by = Auth::user()->id;
                            $UsageRegister->created_at = time();
                            $UsageRegister->save();
                        }

                        // Raw Stock Update
                        $rawStock->quantity -= $usageInput[$i];
                        $rawStock->updated_by = Auth::user()->id;
                        $rawStock->updated_at = time();
                        $rawStock->update();
                    } else {
                        Session()->flash('warning_message', 'Alert: Not Enough Stock! Usage Quantity ' . $usageInput[$i] . ' is greater than Raw Material Stock ' . $rawStock->quantity . '');
                        throw new \Exception('error');
                    }
                }
            });
        } catch (\Exception $e) {
            Session()->flash('flash_message', 'Usage Register not done!');
            return redirect('discardedSales');
        }

        Session()->flash('flash_message', 'Usage Register has been created!');
        return redirect('discardedSales');
    }

    public function edit($id)
    {
        $UsageRegister = UsageRegister::findOrFail($id);
        $materials = Material::lists('name', 'id');
        return view('discardedSales.edit', compact('UsageRegister', 'materials'));
    }

    public function update($id, DiscardedSalesRequest $request)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $existingRegister = UsageRegister::where('id', $id)->first();
                $UsageRegister = UsageRegister::findOrFail($id);
                $existingStock = RawStock::where(['year' => CommonHelper::get_current_financial_year(), 'stock_type' => Config::get('common.balance_type_intermediate'), 'material_id' => $existingRegister->material_id])->first();

                if ($existingStock->quantity + $existingRegister->usage >= $request->input('usage')) {
                    $UsageRegister->date = $request->input('date');
                    $UsageRegister->material_id = $request->input('material_id');
                    $UsageRegister->usage = $request->input('usage');
                    $UsageRegister->status = $request->input('status');
                    $UsageRegister->updated_by = Auth::user()->id;
                    $UsageRegister->updated_at = time();
                    $UsageRegister->update();

                    if ($existingRegister->usage != $request->input('usage')) {
                        if ($existingRegister->usage > $request->input('usage')) {
                            $difference = $existingRegister->usage - $request->input('usage');
                            $stock = RawStock::findOrFail($existingStock->id);
                            $stock->quantity = $existingStock->quantity + $difference;
                            $stock->updated_by = Auth::user()->id;
                            $stock->updated_at = time();
                            $stock->update();
                        } elseif ($existingRegister->usage < $request->input('usage')) {
                            $difference = $request->input('usage') - $existingRegister->usage;
                            $stock = RawStock::findOrFail($existingStock->id);
                            $stock->quantity = $existingStock->quantity - $difference;
                            $stock->updated_by = Auth::user()->id;
                            $stock->updated_at = time();
                            $stock->update();
                        }
                    }
                } else {
                    Session()->flash('warning_message', 'Alert: Not Enough Stock! Usage Quantity ' . $request->input('usage') . ' is greater than Raw Material Stock ' . ($existingStock->quantity + $existingRegister->usage) . '');
                    throw new \Exception('error');
                }
            });
        } catch (\Exception $e) {
            Session()->flash('error_message', 'Usage Register not updated!');
            return redirect('discardedSales');
        }

        Session()->flash('flash_message', 'Usage Register has been updated!');
        return redirect('discardedSales');
    }
}
