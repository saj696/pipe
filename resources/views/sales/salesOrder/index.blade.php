@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Sales Order
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/salesOrder/create' )}}">Add New Sales Order</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>
                                    Customer
                                </th>

                                <th>
                                    Order Place
                                </th>
                                <th>
                                    Order Date
                                </th>
                                <th>
                                    Total Amount
                                </th>
                                <th>
                                    Delivery Status
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>

                            @if(sizeof($salesOrders)>0)
                                @foreach($salesOrders as $salesOrder)
                                    <tr>
                                        <td>{{ \App\Helpers\CommonHelper::getCustomerName($salesOrder->customer_id,$salesOrder->customer_type) }}</td>
                                        <td>{{ $salesOrder->workspaces->name }}</td>
                                        <td>{{ date('d-m-Y',$salesOrder->created_at) }}</td>
                                        <td>{{ $salesOrder->total}}</td>
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
                                            <a class="label label-danger"
                                               href="{{ url('/salesOrder/'.$salesOrder->id.'/edit' )}}">Edit</a>
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
