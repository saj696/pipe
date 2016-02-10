<?php

namespace App\Http\Controllers\User;

use App\Http\Requests;
use App\Models\UserGroup;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserGroupRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class UserGroupsController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $userGroups = UserGroup::where('status', 1)->paginate(5);
        return view('userGroups.index', compact('userGroups'));
    }

    public function show($id)
    {
        $component = Component::findOrFail($id);
        return view('userGroups.show', compact('component'));
    }

    public function create()
    {
        return view('userGroups.create');
    }

    public function store(UserGroupRequest $request)
    {
        $group = New UserGroup;
        $group->name_en = $request->input('name_en');
        $group->name_bn = $request->input('name_bn');
        $group->status = $request->input('status');
        $group->ordering = $request->input('ordering');
        $group->created_by = Auth::user()->id;
        $group->created_at = time();

        $group->save();

        Session()->flash('flash_message', 'Group has been created!');
        return redirect('groups');
    }

    public function edit($id)
    {
        $userGroup = UserGroup::findOrFail($id);
        return view('userGroups.edit', compact('userGroup'));
    }

    public function update($id, UserGroupRequest $request)
    {
        $userGroup = UserGroup::findOrFail($id);

        $userGroup->name_en = $request->input('name_en');
        $userGroup->name_bn = $request->input('name_bn');
        $userGroup->status = $request->input('status');
        $userGroup->ordering = $request->input('ordering');
        $userGroup->updated_by = Auth::user()->id;
        $userGroup->updated_at = time();
        $userGroup->update();

        Session()->flash('flash_message', 'Group has been updated!');
        return redirect('groups');
    }
}
