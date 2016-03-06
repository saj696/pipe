<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Session;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $users = User::with('userGroup')->where('user_group_id', '!=',1)->paginate(5);
        return view('users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function create()
    {
        $groups = UserGroup::where('id', '!=',1)->lists('name_en', 'id');
        $workspaces = Workspace::lists('name', 'id');
        return view('users.create', compact('groups', 'workspaces'));
    }

    public function store(UserRequest $request)
    {
        $user = New User;
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->name_en = $request->input('name_en');
        $user->name_bn = $request->input('name_bn');
        $user->user_group_id = $request->input('user_group_id');
        $user->workspace_id = $request->input('workspace_id');
        $user->status = $request->input('status');
        $user->created_by = Auth::user()->id;
        $user->created_at = time();

        $user->save();

        Session()->flash('flash_message', 'User has been created!');
        return redirect('users');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $groups = UserGroup::lists('name_en', 'id');
        $workspaces = Workspace::lists('name', 'id');
        return view('users.edit', compact('user', 'groups', 'workspaces'));
    }

    public function update($id, UserRequest $request)
    {
        $user = User::findOrFail($id);

        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->name_en = $request->input('name_en');
        $user->name_bn = $request->input('name_bn');
        $user->user_group_id = $request->input('user_group_id');
        $user->workspace_id = $request->input('workspace_id');
        $user->status = $request->input('status');
        $user->updated_by = Auth::user()->id;
        $user->updated_at = time();
        $user->update();

        Session()->flash('flash_message', 'User has been updated!');
        return redirect('users');
    }
}
