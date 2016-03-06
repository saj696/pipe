<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\DesignationRequest;
use App\Models\Designation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Session;

class DesignationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $status = Config::get('common.status');
        $designations = Designation::paginate(Config::get('common.pagination'));
        return view('designations.index', compact('designations', 'status'));
    }

    public function show($id)
    {
//        $designation = designation::findOrFail($id);
//        return view('designations.show', compact('designation'));
    }

    public function create()
    {
        return view('designations.create');
    }

    public function store(DesignationRequest $request)
    {
        $designation = New Designation;
        $designation->name = $request->input('name');
        $designation->salary = $request->input('salary');
        $designation->hourly_rate = $request->input('hourly_rate');
        $designation->created_by = Auth::user()->id;
        $designation->created_at = time();

        $designation->save();

        Session()->flash('flash_message', 'Designation has been created!');
        return redirect('designations');
    }

    public function edit($id)
    {
        $designation = Designation::findOrFail($id);
        return view('designations.edit', compact('designation'));
    }

    public function update($id, DesignationRequest $request)
    {
        $designation = Designation::findOrFail($id);
        $designation->name = $request->input('name');
        $designation->salary = $request->input('salary');
        $designation->hourly_rate = $request->input('hourly_rate');
        $designation->updated_by = Auth::user()->id;
        $designation->updated_at = time();
        $designation->update();

        Session()->flash('flash_message', 'Designation has been updated!');
        return redirect('designations');
    }
}
