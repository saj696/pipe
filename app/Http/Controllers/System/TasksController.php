<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\TaskRequest;
use App\Models\Component;
use App\Models\Module;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Session;

class TasksController extends Controller
{
    public function __construct()
    {
        $this->middleware('perm');
    }

    public function index()
    {
        $tasks = Task::with('component', 'module')
            ->latest()
            ->where('tasks.status', 1)
            ->paginate(Config::get('common.pagination'));

        return view('tasks.index', compact('tasks'));
    }

    public function show($id)
    {
        $task = Task::findOrFail($id);
        return view('tasks.show', compact('task'));
    }

    public function create()
    {
        $components = Component::lists('name_en', 'id');
        $modules = Module::lists('name_en', 'id');
        return view('tasks.create', compact('components', 'modules'));
    }

    public function store(TaskRequest $request)
    {
        $task = New Task;
        $task->name_en = $request->input('name_en');
        $task->name_bn = $request->input('name_bn');
        $task->component_id = $request->input('component_id');
        $task->module_id = $request->input('module_id');
        $task->route = $request->input('route');
        $task->icon = $request->input('icon');
        $task->description = $request->input('description');
        $task->ordering = $request->input('ordering');
        $task->created_by = Auth::user()->id;
        $task->created_at = time();

        $task->save();

        Session()->flash('flash_message', 'Task has been created!');
        return redirect('tasks');
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $components = Component::lists('name_en', 'id');
        $modules = Module::where('component_id', '=', $task->component_id)->lists('name_en', 'id');
        return view('tasks.edit', compact('task', 'modules', 'components'));
    }

    public function update($id, TaskRequest $request)
    {
        $task = Task::findOrFail($id);

        $task->name_en = $request->input('name_en');
        $task->name_bn = $request->input('name_bn');
        $task->component_id = $request->input('component_id');
        $task->module_id = $request->input('module_id');
        $task->route = $request->input('route');
        $task->icon = $request->input('icon');
        $task->description = $request->input('description');
        $task->ordering = $request->input('ordering');
        $task->updated_by = Auth::user()->id;
        $task->updated_at = time();
        $task->update();

        Session()->flash('flash_message', 'Task has been updated!');
        return redirect('tasks');
    }
}
