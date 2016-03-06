@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>User Groups
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/groups/create' )}}">New</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>
                                    ID
                                </th>
                                <th>
                                    Name
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
                            @if(sizeof($userGroups)>0)
                                @foreach($userGroups as $userGroup)
                                    <tr>
                                        <td>
                                            {{ $userGroup->id }}
                                        </td>
                                        <td>
                                            {{ $userGroup->name_en }}
                                        </td>
                                        <td>
                                            {{ $userGroup->status==1?'Active':'Inactive' }}
                                        </td>
                                        <td>
                                            <a class="label label-danger"
                                               href="{{ url('/groups/'.$userGroup->id.'/edit' )}}">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center danger">No Data Found</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination"> {{ $userGroups->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
