@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Employees
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/employees/create' )}}">New</a>
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
                                        Designation
                                    </th>
                                    <th>
                                        Mobile
                                    </th>
                                    <th>
                                        Email
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
                            @if(sizeof($employees)>0)
                            @foreach($employees as $employee)
                            <tr>
                                <td>
                                    {{ $employee->name }}
                                </td>
                                <td>
                                    {{ $employee->designation->name }}
                                </td>
                                <td>
                                    {{ $employee->mobile }}
                                </td>
                                <td>
                                    {{ $employee->email }}
                                </td>
                                <td>
                                    {{ $status[$employee->status] }}
                                </td>
                                <td>
                                    <a class="label label-danger" href="{{ url('/employees/'.$employee->id.'/edit' )}}">Edit</a>
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
                    <div class="pagination"> {{ $employees->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
