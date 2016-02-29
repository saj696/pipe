<?php

namespace App\Http\Controllers\Setup;

use App\Http\Requests;
use App\Models\Workspace;
use App\Models\Component;
use App\Models\WorkspaceLedger;
use App\Models\ChartOfAccount;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkspaceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class WorkspacesController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $workspaces = Workspace::with('parentInfo')->paginate(Config::get('common.pagination'));
        $workspace_types = Config::get('common.workspace_type');
        $status = Config::get('common.status');
        return view('workspaces.index', compact('workspaces', 'workspace_types', 'status'));
    }

    public function show($id)
    {
        $workspace = Workspace::findOrFail($id);
        return view('workspaces.show', compact('workspace'));
    }

    public function create()
    {
        $types = Config::get('common.workspace_type');
        $parents = Workspace::lists('name', 'id');
        return view('workspaces.create', compact('types', 'parents'));
    }

    public function store(WorkspaceRequest $request)
    {
        $workspace = New Workspace;
        $workspace->name = $request->input('name');
        $workspace->type = $request->input('type');
        $workspace->parent = $request->input('parent');
        $workspace->location = $request->input('location');
        $workspace->status = $request->input('status');
        $workspace->created_by = Auth::user()->id;
        $workspace->created_at = time();
        $workspace->save();

        Session()->flash('flash_message', 'Workspace has been created!');
        return redirect('workspaces');
    }

    public function edit($id)
    {
        $types = Config::get('common.workspace_type');
        $parents = Workspace::lists('name', 'id');
        $workspace = Workspace::findOrFail($id);
        return view('workspaces.edit', compact('types', 'parents', 'workspace'));
    }

    public function update($id, WorkspaceRequest $request)
    {
        $workspace = Workspace::findOrFail($id);

        $workspace->name = $request->input('name');
        $workspace->type = $request->input('type');
        $workspace->parent = $request->input('parent');
        $workspace->location = $request->input('location');
        $workspace->status = $request->input('status');
        $workspace->updated_by = Auth::user()->id;
        $workspace->updated_at = time();
        $workspace->update();

        Session()->flash('flash_message', 'Workspace has been updated!');
        return redirect('workspaces');
    }
}
