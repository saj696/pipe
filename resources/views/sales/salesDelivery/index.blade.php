@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green-seagreen">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Sales Delivery
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
                                    Order Type
                                </th>
                                <th>
                                    Order Place
                                </th>
                                <th>
                                    Order Date
                                </th>
                                <th>
                                    Product Quantity
                                </th>
                                <th>
                                    Delivery Status
                                </th><th>
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>

                            @if(isset($salesOrders))
                                @foreach($salesOrders as $salesOrder)
                                    <tr>
                                        <td>{{ \App\Helpers\CommonHelper::getCustomerName($salesOrder->customer_id,$salesOrder->customer_type) }}</td>
                                        <td>{{ ($salesOrder->order_type==1)? 'Sales' : 'Replacement' }}</td>
                                        <td>{{ $salesOrder->workspaces->name }}</td>
                                        <td>{{ date('d-m-Y',$salesOrder->created_at) }}</td>
                                        <td>{{ $salesOrder->salesOrderItems->count() }}</td>
                                        <td>
                                            @if($salesOrder->status==1)
                                                <span class="label label-danger">Not Yet</span>
                                            @elseif($salesOrder->status==2)
                                                <span class="label label-warning">Partial</span>
                                            @elseif($salesOrder->status==4)
                                                <span class="label label-success">Delivered</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="label label-success"
                                               href="{{ url('salesDelivery/'.$salesOrder->id.'/edit') }}">Deliver</a>
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
                </div>
            </div>
        </div>
    </div>
@stop
