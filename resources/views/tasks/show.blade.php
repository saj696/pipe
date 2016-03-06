@extends('layouts.app')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Task Detail
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/tasks' )}}">Back</a>
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
                                    Route
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
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    {{ $task->name_en }}
                                </td>
                                <td>
                                    <p>{{ $task->component->name_en }}</p>
                                </td>
                                <td>
                                    <p>{{ $task->module->name_en }}</p>
                                </td>
                                <td>
                                    <p>{{ $task->route }}</p>
                                </td>
                                <td>
                                    <p>{{ $task->description }}</p>
                                </td>
                                <td>
                                    {{ $task->icon }}
                                </td>
                                <td>
                                    {{ $task->ordering }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END BORDERED TABLE PORTLET-->
        </div>
    </div>
@stop