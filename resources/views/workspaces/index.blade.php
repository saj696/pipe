@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Workspaces
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/workspaces/create' )}}">New</a>
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
                                        Type
                                    </th>
                                    <th>
                                        Parent
                                    </th>
                                    <th>
                                        Location
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(sizeof($workspaces)>0)
                                @foreach($workspaces as $workspace)
                                    <tr>
                                        <td>
                                            {{ $workspace->name }}
                                        </td>
                                        <td>
                                            {{ $workspace_types[$workspace->type] }}
                                        </td>
                                        <td>
                                            {{ isset($workspace->parentInfo->name)?$workspace->parentInfo->name:'No Parent' }}
                                        </td>
                                        <td>
                                            {{ $workspace->location }}
                                        </td>
                                        <td>
                                            {{ $status[$workspace->status] }}
                                        </td>
                                        <td>
                                            <a class="label label-danger" href="{{ url('/workspaces/'.$workspace->id.'/edit' )}}">Edit</a>
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
                    <div class="pagination"> {{ $workspaces->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
