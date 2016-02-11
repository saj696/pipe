<?php

namespace App\Http\Controllers\Setup;

use App\Http\Requests;
use App\Models\Material;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class MaterialsController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $materials = Material::paginate(Config::get('common.pagination'));
        $types = Config::get('common.material_type');
        $status = Config::get('common.status');
        return view('materials.index', compact('materials', 'types', 'status'));
    }

    public function show($id)
    {
        $material = Material::findOrFail($id);
        return view('materials.show', compact('material'));
    }

    public function create()
    {
        $types = Config::get('common.material_type');
        return view('materials.create', compact('types'));
    }

    public function store(MaterialRequest $request)
    {
        $material = New Material;
        $material->name = $request->input('name');
        $material->type = $request->input('type');
        $material->status = $request->input('status');
        $material->created_by = Auth::user()->id;
        $material->created_at = time();

        $material->save();

        Session()->flash('flash_message', 'Material has been created!');
        return redirect('materials');
    }

    public function edit($id)
    {
        $types = Config::get('common.material_type');
        $material = Material::findOrFail($id);
        return view('materials.edit', compact('types', 'material'));
    }

    public function update($id, MaterialRequest $request)
    {
        $material = Material::findOrFail($id);

        $material->name = $request->input('name');
        $material->type = $request->input('type');
        $material->status = $request->input('status');
        $material->updated_by = Auth::user()->id;
        $material->updated_at = time();
        $material->update();

        Session()->flash('flash_message', 'Material has been updated!');
        return redirect('materials');
    }
}
