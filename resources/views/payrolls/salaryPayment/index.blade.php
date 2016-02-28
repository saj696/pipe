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
                           href="{{ url('/salary_payment/create' )}}">Payment Salary</a>
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
                                    Paid Salary
                                </th>
                                <th>
                                    Action
                                </th>

                            </tr>
                            </thead>
                            <tbody>

                            @if(sizeof($salaryPayments)>0)
                                @foreach($salaryPayments as $salaryPayment)
                                    <tr>
                                        <td>{{ $salaryPayment->employee->name }}</td>
                                        <td>{{ $salaryPayment->year }}</td>
                                        <td>{{ date('F', mktime(0, 0, 0, $salaryPayment->month)) }}</td>
                                        <td>{{ $salaryPayment->amount }}</td>
                                        <td>
                                            <a class="label label-danger" href="{{ url('/salary_payment/'.$salaryPayment->id.'/edit' )}}">Edit</a>
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
                    <div class="pagination"> {{ $salaryPayments->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
