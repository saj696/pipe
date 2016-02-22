@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Account Initialization
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        Workspace
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
                                        @if($workspace->workspaceLedger->isEmpty())
                                            <a class="label label-danger" href="{{ url('/initializations/'.$workspace->id.'/edit' )}}">Initialize</a>
                                        @else
                                            <a class="label label-success" href="#">Initialized</a>
                                        @endif
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
                    <div class="pagination"> {{ $workspaces->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
