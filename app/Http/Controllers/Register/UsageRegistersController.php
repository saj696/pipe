<?php

namespace App\Http\Controllers\Register;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\UsageRegisterRequest;
use App\Models\Material;
use App\Models\RawStock;
use App\Models\UsageRegister;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Session;

class UsageRegistersController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $usageRegisters = UsageRegister::paginate(Config::get('common.pagination'));
        $materials = Material::lists('name', 'id');
        $status = Config::get('common.status');
        return view('usageRegisters.index', compact('usageRegisters', 'materials', 'status'));
    }

    public function show($id)
    {
        $UsageRegister = UsageRegister::findOrFail($id);
        return view('usageRegisters.show', compact('UsageRegister'));
    }

    public function create()
    {
        $materials = Material::lists('name', 'id');
        return view('usageRegisters.create', compact('materials'));
    }

    public function store(UsageRegisterRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $count = sizeof($request->input('material_id'));
                $materialInput = $request->input('material_id');
                $usageInput = $request->input('usage');

                for ($i = 0; $i < $count; $i++) {
                    $rawStock = RawStock::where(['material_id' => $materialInput[$i], 'year' => CommonHelper::get_current_financial_year(), 'stock_type' => Config::get('common.balance_type_intermediate')])->first();

                    if ($rawStock->quantity >= $usageInput[$i]) {
                        $UsageRegister = New UsageRegister;
                        $UsageRegister->date = $request->input('date');
                        $UsageRegister->material_id = $materialInput[$i];
                        $UsageRegister->usage = $usageInput[$i];
                        $UsageRegister->created_by = Auth::user()->id;
                        $UsageRegister->created_at = time();
                        $UsageRegister->save();

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
            return redirect('usageRegisters');
        }

        Session()->flash('flash_message', 'Usage Register has been created!');
        return redirect('usageRegisters');
    }

    public function edit($id)
    {
        $UsageRegister = UsageRegister::findOrFail($id);
        $materials = Material::lists('name', 'id');
        return view('usageRegisters.edit', compact('UsageRegister', 'materials'));
    }

    public function update($id, UsageRegisterRequest $request)
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
            return redirect('usageRegisters');
        }

        Session()->flash('flash_message', 'Usage Register has been updated!');
        return redirect('usageRegisters');
    }
}
