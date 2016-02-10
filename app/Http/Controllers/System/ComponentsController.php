<?php

namespace App\Http\Controllers\System;

use App\Http\Requests;
use App\Models\Component;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\ComponentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class ComponentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $components = Component::latest()->where('status', 1)->paginate(5);
        return view('components.index', compact('components'));
    }

    public function show($id)
    {
        $component = Component::findOrFail($id);
        return view('components.show', compact('component'));
    }

    public function create()
    {
        return view('components.create');
    }

    public function store(ComponentRequest $request)
    {
        $component = New Component;
        $component->name_en = $request->input('name_en');
        $component->name_bn = $request->input('name_bn');
        $component->icon = $request->input('icon');
        $component->description = $request->input('description');
        $component->ordering = $request->input('ordering');
        $component->created_by = Auth::user()->id;
        $component->created_at = time();

        $component->save();

        Session()->flash('flash_message', 'Component has been created!');
        return redirect('components');
    }

    public function edit($id)
    {
        $component = Component::findOrFail($id);
        return view('components.edit', compact('component'));
    }

    public function update($id, ComponentRequest $request)
    {
        $component = Component::findOrFail($id);
        $component->update($request->all());

        Session()->flash('flash_message', 'Component has been updated!');
        return redirect('components');
    }
}
