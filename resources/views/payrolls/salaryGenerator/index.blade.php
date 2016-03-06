@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Sales Generator
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/salary_generator/create' )}}">Generate Salary</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>
                                    Employee Name
                                </th>
                                <th>
                                    Year
                                </th>
                                <th>
                                    Month
                                </th>
                                <th>
                                    Base Salary
                                </th>
                                <th>
                                    Extra Hours
                                </th>
                                <th>
                                    Cut
                                </th>
                                <th>
                                    Bonus
                                </th>
                                <th>
                                    Net Salary
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>

                            @if(sizeof($lists)>0)
                                @foreach($lists as $list)
                                    <tr>
                                        <td>{{ $list->employee->name }}</td>
                                        <td>{{ $list->year }}</td>
                                        <td>{{ date('F', mktime(0, 0, 0, $list->month)) }}</td>
                                        <td>{{ $list->salary }}</td>
                                        <td>{{ $list->extra_hours}}</td>
                                        <td>{{ $list->cut}}</td>
                                        <td>{{ $list->bonus}}</td>
                                        <td>{{ $list->net}}</td>
                                        <td>
                                            <a class="label label-danger"
                                               href="{{ url('/salary_generator/'.$list->id.'/edit' )}}">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="12" class="text-center danger">No Data Found</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination"> {{ $lists->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
