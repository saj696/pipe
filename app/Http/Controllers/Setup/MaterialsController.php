<?php

namespace App\Http\Controllers\Setup;

use App\Helpers\CommonHelper;
use App\Http\Requests;
use App\Models\Material;
use App\Models\RawStock;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Session;
use DB;
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
        try
        {
            DB::transaction(function () use ($request)
            {
                $material = New Material;
                $material->name = $request->input('name');
                $material->type = $request->input('type');
                $material->status = $request->input('status');
                $material->created_by = Auth::user()->id;
                $material->created_at = time();
                $material->save();
                $insertedId = $material->id;

                // Current Year Opening Stock
                $rawStock = New RawStock;
                $rawStock->material_id = $insertedId;
                $rawStock->year = CommonHelper::get_current_financial_year();
                $rawStock->stock_type = Config::get('common.balance_type_opening');
                $rawStock->created_by = Auth::user()->id;
                $rawStock->created_at = time();
                $rawStock->save();

                // Current Year Intermediate Stock
                $rawStock = New RawStock;
                $rawStock->material_id = $insertedId;
                $rawStock->year = CommonHelper::get_current_financial_year();
                $rawStock->stock_type = Config::get('common.balance_type_intermediate');
                $rawStock->created_by = Auth::user()->id;
                $rawStock->created_at = time();
                $rawStock->save();
            });
        }
        catch (\Exception $e)
        {
            Session()->flash('error_message', 'Material not created!');
            return redirect('materials');
        }

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
