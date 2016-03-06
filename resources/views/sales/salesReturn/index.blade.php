@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box blue-hoki">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Sales Return
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/sales_return/create' )}}">New</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table">
                        <table class="table table-striped table-bordered table-hover" id="sample_6">
                            <thead>
                            <tr>
                                <th>
                                    Customer
                                </th>
                                <th>
                                    Customer Type
                                </th>
                                <th>
                                    Quantity Returned
                                </th>
                                <th>
                                    Total Amount
                                </th>
                                {{--<th>--}}
                                    {{--Action--}}
                                {{--</th>--}}
                            </tr>
                            </thead>
                            <tbody>

                            @if(isset($salesReturns))
                                @foreach($salesReturns as $salesReturn)
                                    <tr>
                                        <td>{{ \App\Helpers\CommonHelper::getCustomerName($salesReturn->customer_id,$salesReturn->customer_type) }}</td>
                                        <td>{{ Config::get('common.sales_customer_type.'.$salesReturn->customer_type) }}</td>
                                        <td>{{ $salesReturn->quantity }}</td>
                                        <td>{{ $salesReturn->total_amount}}</td>
                                        {{--<td>--}}
                                            {{--&nbsp;--}}
                                        {{--</td>--}}
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
                </div>
            </div>
        </div>
    </div>
@stop
