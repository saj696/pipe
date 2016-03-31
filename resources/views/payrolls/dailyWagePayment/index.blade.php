@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box blue-hoki">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Wage Payment
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/daily_wage_payment/create' )}}">Payment Today</a>
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
                                    Date
                                </th>
                                <th>
                                    Wage
                                </th>
                                <th>
                                    Due
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(sizeof($wages) > 0)
                                @foreach($wages as $wage)
                                    <tr>
                                        <td>{{ $wage->employee->name }}</td>
                                        <td>{{ date('d-m-Y',$wage->payment_date) }}</td>
                                        <td>{{ $wage->wage }}</td>
                                        <td>{{ $wage->due}}</td>
                                        <td>
                                            <a class="label label-danger"
                                               href="{{ url('/daily_wage_payment/'.$wage->id.'/edit' )}}">Edit</a>
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
                    <div class="pagination"> {{ $wages->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@endsection