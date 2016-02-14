<?php

namespace App\Http\Controllers\Register;

use App\Http\Requests;
use App\Models\UsageRegister;
use App\Models\Material;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\UsageRegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

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
        $count = sizeof($request->input('material_id'));
        $materialInput = $request->input('material_id');
        $usageInput = $request->input('usage');

        for($i=0; $i<$count; $i++)
        {
            $UsageRegister = New UsageRegister;
            $UsageRegister->date = $request->input('date');
            $UsageRegister->material_id = $materialInput[$i];
            $UsageRegister->usage = $usageInput[$i];
            $UsageRegister->created_by = Auth::user()->id;
            $UsageRegister->created_at = time();

            $UsageRegister->save();
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
        $UsageRegister = UsageRegister::findOrFail($id);

        $UsageRegister->date = $request->input('date');
        $UsageRegister->material_id = $request->input('material_id');
        $UsageRegister->usage = $request->input('usage');
        $UsageRegister->status = $request->input('status');
        $UsageRegister->updated_by = Auth::user()->id;
        $UsageRegister->updated_at = time();
        $UsageRegister->update();

        Session()->flash('flash_message', 'Usage Register has been updated!');
        return redirect('usageRegisters');
    }
}
