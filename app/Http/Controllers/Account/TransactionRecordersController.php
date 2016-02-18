<?php

namespace App\Http\Controllers\Account;

use App\Http\Requests;
use App\Models\TransactionRecorder;
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

class TransactionRecordersController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $recorders = TransactionRecorder::paginate(Config::get('common.pagination'));
        $status = Config::get('common.status');
        return view('transactionRecorders.index', compact('recorders', 'status'));
    }

    public function create()
    {
        $types = Config::get('common.material_type');
        return view('materials.create', compact('types'));
    }

    public function store(MaterialRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $material = New Material;
            $material->name = $request->input('name');
            $material->type = $request->input('type');
            $material->status = $request->input('status');
            $material->created_by = Auth::user()->id;
            $material->created_at = time();
            $material->save();
            $insertedId = $material->id;

            $rawStock = New RawStock;
            $rawStock->material_id = $insertedId;
            $rawStock->created_by = Auth::user()->id;
            $rawStock->created_at = time();
            $rawStock->save();

            DB::commit();
            Session()->flash('flash_message', 'Material has been created!');
        }
        catch (\Exception $e)
        {
            DB::rollback();
            Session()->flash('flash_message', 'Material not created!');
        }

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
