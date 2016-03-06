<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 13-Jan-16
 * Time: 4:45 PM
 */

namespace App\Helpers;

use DB;
use Illuminate\Support\Facades\Auth;


class UserHelper
{
    public static function getUserGroupRoleDetail($user_group_id)
    {
        $sum = DB::table('user_group_roles')
            ->select('user_group_id', DB::raw('Count(DISTINCT component_id) as total_component'), DB::raw('Count(DISTINCT module_id) as total_module'), DB::raw('Count(DISTINCT task_id) as total_task'), DB::raw('Max(created_at) as last_create_date'), DB::raw('Max(updated_at) as last_update_date'))
            ->where('user_group_id', $user_group_id)
            ->where('list', 1)
            ->first();
        return $sum;
    }

    public static function get_task($position = null, $action = 'list')
    {
        $user = Auth::user();

        $tasks = DB::table('tasks as task')
            ->select(
                'ugr.id as role_id',
                'ugr.user_group_id',
                'components.name_en as component_name',
                'modules.name_en as module_name',
                'task.name_en as task_name',
                'task.id', 'task.component_id', 'task.module_id', 'task.route', 'task.icon as task_icon', 'modules.icon as module_icon', 'components.icon as component_icon'
            )
            ->join('user_group_roles as ugr', 'ugr.task_id', '=', 'task.id')
            ->join('components', 'components.id', '=', 'task.component_id')
            ->join('modules', 'modules.id', '=', 'task.module_id')
            ->where('ugr.user_group_id', $user->user_group_id)
            ->where('task.status', 1)
            ->where('modules.status', 1)
            ->where('components.status', 1)
            ->where('task.' . $position, 1)
            ->where('ugr.' . $action, 1)
            ->orderBy('components.ordering', 'asc')
            ->orderBy('modules.ordering', 'asc')
            ->orderBy('task.ordering', 'asc')
            ->get();

        return $tasks;
    }

    public static function get_task_module($position = null, $action = 'list')
    {
        $tasks = UserHelper::get_task($position, $action);
        $modules = array();
        foreach ($tasks as $task) {
            $modules[$task->module_id]['component_id'] = $task->component_id;
            $modules[$task->module_id]['component_name'] = $task->component_name;
            $modules[$task->module_id]['module_name'] = $task->module_name;
            $modules[$task->module_id]['id'] = $task->module_id;
            $modules[$task->module_id]['module_icon'] = $task->module_icon;
            $modules[$task->module_id]['component_icon'] = $task->component_icon;
            $modules[$task->module_id]['tasks'][] = $task;
        }
        return $modules;
    }

    public static function get_task_module_component($position = null, $action = 'list')
    {
        $modules = UserHelper::get_task_module($position, $action);
        $components = array();
        foreach ($modules as $module) {
            $components[$module['component_id']]['id'] = $module['component_id'];
            $components[$module['component_id']]['component_name'] = $module['component_name'];
            $components[$module['component_id']]['component_icon'] = $module['component_icon'];
            $components[$module['component_id']]['modules'][] = $module;
        }
        return $components;
    }

    public static function get_route_permission($route)
    {
        $user_group_id = Auth::user()->user_group_id;

        $perm = DB::table('user_group_roles as ugr')
            ->select('ugr.*')
            ->join('tasks as task', 'task.id', '=', 'ugr.task_id')
            ->where('task.route', 'like', $route . '%')
            ->where('ugr.user_group_id', $user_group_id)
            ->first();
        return $perm;
    }

    public static function get_module_name($route)
    {
        $module_id = Task::where('route', $route)->value('module_id');
        return $module_id;
    }
}