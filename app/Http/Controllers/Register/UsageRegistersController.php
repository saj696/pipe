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
        $status = Config::get('common.status');
        return view('usageRegisters.index', compact('usageRegisters', 'status'));
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
        $UsageRegister = New UsageRegister;
        $UsageRegister->name = $request->input('name');
        $UsageRegister->type = $request->input('type');
        $UsageRegister->status = $request->input('status');
        $UsageRegister->created_by = Auth::user()->id;
        $UsageRegister->created_at = time();

        $UsageRegister->save();

        Session()->flash('flash_message', 'Usage Register has been created!');
        return redirect('usageRegisters');
    }

    public function edit($id)
    {
        $types = Config::get('common.UsageRegister_type');
        $UsageRegister = UsageRegister::findOrFail($id);
        return view('usageRegisters.edit', compact('types', 'UsageRegister'));
    }

    public function update($id, UsageRegisterRequest $request)
    {
        $UsageRegister = UsageRegister::findOrFail($id);

        $UsageRegister->name = $request->input('name');
        $UsageRegister->type = $request->input('type');
        $UsageRegister->status = $request->input('status');
        $UsageRegister->updated_by = Auth::user()->id;
        $UsageRegister->updated_at = time();
        $UsageRegister->update();

        Session()->flash('flash_message', 'Usage Register has been updated!');
        return redirect('usageRegisters');
    }
}
