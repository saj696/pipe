<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\ModuleRequest;
use App\Models\Component;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;
use Session;

class ModulesController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $modules = Module::with('component')
            ->latest()
            ->where('modules.status', 1)
            ->paginate(5);

        return view('modules.index', compact('modules'));
    }

    public function show($id)
    {
        $module = Module::findOrFail($id);
        return view('modules.show', compact('module'));
    }

    public function create()
    {
        $components = Component::lists('name_en', 'id');
        return view('modules.create', compact('components'));
    }

    public function store(ModuleRequest $request)
    {
        $module = New Module;
        $module->name_en = $request->input('name_en');
        $module->name_bn = $request->input('name_bn');
        $module->component_id = $request->input('component_id');
        $module->icon = $request->input('icon');
        $module->description = $request->input('description');
        $module->ordering = $request->input('ordering');
        $module->created_by = Auth::user()->id;
        $module->created_at = time();

        $module->save();

        Session()->flash('flash_message', 'Module has been created!');
        return redirect('modules');
    }

    public function edit($id)
    {
        $components = Component::lists('name_en', 'id');
        $module = Module::findOrFail($id);
        return view('modules.edit', compact('module', 'components'));
    }

    public function update($id, ModuleRequest $request)
    {
        $module = Module::findOrFail($id);

        $module->name_en = $request->input('name_en');
        $module->name_bn = $request->input('name_bn');
        $module->component_id = $request->input('component_id');
        $module->icon = $request->input('icon');
        $module->description = $request->input('description');
        $module->ordering = $request->input('ordering');
        $module->updated_by = Auth::user()->id;
        $module->updated_at = time();
        $module->update();

        Session()->flash('flash_message', 'Module has been updated!');
        return redirect('modules');
    }
}
