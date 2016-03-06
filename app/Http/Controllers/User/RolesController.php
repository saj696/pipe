<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\RoleRequest;
use App\Models\Task;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserGroupRole;
use DB;
use Illuminate\Support\Facades\Auth;
use Session;
use stdClass;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
//        $subQuery = DB::table('user_group_roles')
//        ->select('user_group_id', DB::raw('Count(DISTINCT component_id) as total_component'), DB::raw('Count(DISTINCT module_id) as total_module'), DB::raw('Count(DISTINCT task_id) as total_task'), DB::raw('Max(created_at) as last_create_date'), DB::raw('Max(updated_at) as last_update_date'))
//                ->groupBy('user_group_id')
//                ->where('list',1);

//        $groups = DB::table(\DB::raw(' ( ' . $subQuery->toSql() . ' ) as ugr '))
//                ->mergeBindings($subQuery)
//                ->selectRaw('ugr.total_component,ugr.total_module,ugr.total_task,ugr.last_create_date,ugr.last_update_date')
//                ->leftJoin('user_groups ug', 'ugr.user_group_id', '=', 'user_groups.id')
//                ->join('user_groups ug', function ($join) {
//                    $join->where('ugr.user_group_id', '=', 'ug.id');
//                })
//                ->get();

//        $groups = DB::table('user_groups')
//                ->select('user_groups.id','user_groups.name_en as group_name')
//                ->from(\DB::raw(' ( ' . $subQuery->toSql() . ' ) as ugr '))
//                ->from(DB::raw("({$subQuery->toSql()} as ugr"))
//                ->selectRaw('ugr.total_component,ugr.total_module,ugr.total_task,ugr.last_create_date,ugr.last_update_date')
//                ->mergeBindings($subQuery)
//                ->leftJoin('ugr', 'ugr.user_group_id', '=', 'user_groups.id')
//                ->get();

        $groups = DB::table('user_groups')
            ->paginate(5);

//        dd($groups);
        return view('roles.index', compact('groups'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function create()
    {
        $groups = UserGroup::lists('name_en', 'id');
        return view('users.create', compact('groups'));
    }

    public function edit($id)
    {
        $user_group = Auth::user()->user_group_id;

        if ($user_group == 1) {
            $tasks = Task::with('component', 'module')->get();

            foreach ($tasks as &$task) {
                $task->list = 1;
                $task->view = 1;
                $task->add = 1;
                $task->edit = 1;
                $task->delete = 1;
                $task->report = 1;
                $task->print = 1;
            }
        } else {
            if (($user_group == $id) || ($id == 1)) {
                $tasks = DB::table('user_group_roles as ugr')
                    ->select(
                        'ugr.component_id',
                        'ugr.module_id',
                        'ugr.task_id',
                        'ugr.list',
                        'ugr.view',
                        'ugr.add',
                        'ugr.edit',
                        'ugr.delete',
                        'ugr.report',
                        'ugr.print',
                        'components.name_en as component_name',
                        'modules.name_en as module_name',
                        'tasks.name_en as task_name'
                    )
                    ->join('tasks', 'tasks.id', '=', 'ugr.task_id')
                    ->join('components', 'components.id', '=', 'ugr.component_id')
                    ->join('modules', 'modules.id', '=', 'ugr.module_id')
                    ->where('tasks.route', 'not like', 'roles%')
                    ->where('ugr.user_group_id', $id)
                    ->where('ugr.list', 1)
                    ->get();
            } else {
                $tasks = DB::table('user_group_roles as ugr')
                    ->select(
                        'ugr.component_id',
                        'ugr.module_id',
                        'ugr.task_id',
                        'ugr.list',
                        'ugr.view',
                        'ugr.add',
                        'ugr.edit',
                        'ugr.delete',
                        'ugr.report',
                        'ugr.print',
                        'components.name_en as component_name',
                        'modules.name_en as module_name',
                        'tasks.name_en as task_name'
                    )
                    ->join('tasks', 'tasks.id', '=', 'ugr.task_id')
                    ->join('components', 'components.id', '=', 'ugr.component_id')
                    ->join('modules', 'modules.id', '=', 'ugr.module_id')
                    ->where('ugr.user_group_id', $id)
                    ->where('ugr.list', 1)
                    ->get();
            }
        }

        $roleResult = DB::table('user_group_roles as ugr')
            ->select('ugr.id as ugr_id', 'ugr.list', 'ugr.view', 'ugr.add', 'ugr.edit', 'ugr.delete', 'ugr.report', 'ugr.print', 'ugr.component_id', 'ugr.module_id', 'ugr.task_id')
            ->where('ugr.user_group_id', $id)
            ->orderBy('ugr.component_id', 'asc')
            ->orderBy('ugr.module_id', 'asc')
            ->get();

        $roles = new stdClass;
        $roles->list = [];
        $roles->view = [];
        $roles->add = [];
        $roles->edit = [];
        $roles->delete = [];
        $roles->report = [];
        $roles->print = [];
        $roles->ugr_id = [];

        foreach ($roleResult as $result) {
            $roles->ugr_id[$result->task_id] = $result->ugr_id;
            if ($result->list) {
                $roles->list[] = $result->task_id;
            }
            if ($result->view) {
                $roles->view[] = $result->task_id;
            }
            if ($result->add) {
                $roles->add[] = $result->task_id;
            }
            if ($result->edit) {
                $roles->edit[] = $result->task_id;
            }
            if ($result->delete) {
                $roles->delete[] = $result->task_id;
            }
            if ($result->report) {
                $roles->report[] = $result->task_id;
            }
            if ($result->print) {
                $roles->print[] = $result->task_id;
            }
        }

        return view('roles.edit', compact('tasks', 'roles', 'id'));
    }

    public function update($id, RoleRequest $request)
    {
        $tasks = $request->input('tasks');

        foreach ($tasks as $task) {
            $data = [];
            $userGroupRole = new UserGroupRole;
            if (isset($task['list']) && $task['list'] == 1) {
                $data['list'] = 1;
            } else {
                $data['list'] = 0;
            }
            if (isset($task['view']) && $task['view'] == 1) {
                $data['view'] = 1;
            } else {
                $data['view'] = 0;
            }
            if (isset($task['add']) && $task['add'] == 1) {
                $data['add'] = 1;
            } else {
                $data['add'] = 0;
            }
            if (isset($task['edit']) && $task['edit'] == 1) {
                $data['edit'] = 1;
            } else {
                $data['edit'] = 0;
            }
            if (isset($task['delete']) && $task['delete'] == 1) {
                $data['delete'] = 1;
            } else {
                $data['delete'] = 0;
            }
            if (isset($task['report']) && $task['report'] == 1) {
                $data['report'] = 1;
            } else {
                $data['report'] = 0;
            }
            if (isset($task['print']) && $task['print'] == 1) {
                $data['print'] = 1;
            } else {
                $data['print'] = 0;
            }

            if (($data['view']) || ($data['add']) || ($data['edit']) || ($data['delete']) || ($data['report']) || ($data['print'])) {
                $data['list'] = 1;
            }

            if ($task['ugr_id'] > 0) {
                $data['updated_by'] = Auth::user()->id;
                $data['updated_at'] = time();
                $userGroupRole->where(['id' => $task['ugr_id']])->update($data);
            } else {
                $data['user_group_id'] = $id;
                $data['component_id'] = $task['component_id'];
                $data['module_id'] = $task['module_id'];
                $data['task_id'] = $task['task_id'];
                $data['created_by'] = Auth::user()->id;
                $data['created_at'] = time();
                DB::table('user_group_roles')->insert($data);
            }
        }

        Session()->flash('flash_message', 'Role has been updated!');
        return redirect('roles');
    }
}
