@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Tasks
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/tasks/create' )}}">New</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Component
                                </th>
                                <th>
                                    Module
                                </th>
                                <th>
                                    Description
                                </th>
                                <th>
                                    Icon
                                </th>
                                <th>
                                    Order
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(sizeof($tasks)>0)
                                @foreach($tasks as $task)
                                    <tr>
                                        <td>
                                            <a href="{{ url('/tasks', $task->id )}}">{{ $task->name_en }}</a>
                                        </td>
                                        <td>
                                            {{ $task->component->name_en }}
                                        </td>
                                        <td>
                                            {{ $task->module->name_en }}
                                        </td>
                                        <td>
                                            {{ str_limit($task->description, 50) }}
                                        </td>
                                        <td>
                                            {{ $task->icon }}
                                        </td>
                                        <td>
                                            {{ $task->ordering }}
                                        </td>
                                        <td>
                                            <a class="label label-danger" href="{{ url('/tasks/'.$task->id.'/edit' )}}">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center danger">No Data Found</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination"> {{ $tasks->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
