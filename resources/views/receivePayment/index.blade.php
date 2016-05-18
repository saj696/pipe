@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Receive Payment
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/receive_payments/create' )}}">New</a>
                    </div>
                </div>

                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>
                                    Date
                                </th>
                                <th>
                                    Account
                                </th>
                                <th>
                                    Total Amount
                                </th>
                                <th>
                                    Amount
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(sizeof($payments)>0)
                                @foreach($payments as $payment)
                                    <tr>
                                        <td>
                                            {{ $payment->date }}
                                        </td>
                                        <td>
                                            {{ $accounts[$payment->account_code] }}
                                        </td>
                                        <td>
                                            {{ isset($payment->total_amount)?$payment->total_amount:'N/A' }}
                                        </td>
                                        <td>
                                            {{ $payment->amount }}
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
                    <div class="pagination"> {{ $payments->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
