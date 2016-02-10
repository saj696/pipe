@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Modules
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/modules/create' )}}">New</a>
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
                            @if(sizeof($modules)>0)
                            @foreach($modules as $module)
                            <tr>
                                <td>
                                    <a href="{{ url('/modules', $module->id )}}">{{ $module->name_en }}</a>
                                </td>
                                <td>
                                    {{ $module->component->name_en }}
                                </td>
                                <td>
                                    {{ str_limit($module->description, 50) }}
                                </td>
                                <td>
                                    {{ $module->icon }}
                                </td>
                                <td>
                                    {{ $module->ordering }}
                                </td>
                                <td>
                                    <a class="label label-danger" href="{{ url('/modules/'.$module->id.'/edit' )}}">Edit</a>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="6" class="text-center danger">No Data Found</td>
                            </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination"> {{ $modules->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
