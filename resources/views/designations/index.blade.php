@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Designations
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/designations/create' )}}">New</a>
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
                                    Base Amount
                                </th>
                                <th>
                                    Hourly Rate
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
                            @if(sizeof($designations)>0)
                                @foreach($designations as $designation)
                                    <tr>
                                        <td>
                                            {{ $designation->name }}
                                        </td>
                                        <td>
                                            {{ $designation->salary }}
                                        </td>
                                        <td>
                                            {{ $designation->hourly_rate>0?$designation->hourly_rate:'N/A' }}
                                        </td>
                                        <td>
                                            {{ $status[$designation->status] }}
                                        </td>
                                        <td>
                                            <a class="label label-danger"
                                               href="{{ url('/designations/'.$designation->id.'/edit' )}}">Edit</a>
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
                    <div class="pagination"> {{ $designations->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
