@extends('layouts.app')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Purchase Detail
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/purchases' )}}">Back</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <th>Supplier</th>
                                    <td>{{$purchase->supplier->company_name}}</td>
                                </tr>
                                <tr>
                                    <th>Purchase Date</th>
                                    <td>{{$purchase->purchase_date}}</td>
                                </tr>
                                <tr>
                                    <th>Transportation Cost</th>
                                    <td>{{$purchase->transportation_cost}}</td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td>{{$purchase->total}}</td>
                                </tr>
                                <tr>
                                    <th>Paid</th>
                                    <td>{{$purchase->paid}}</td>
                                </tr>
                                @if(($purchase->total-$purchase->paid) > 0)
                                    <tr>
                                        <th>Due</th>
                                        <td><span class="badge badge-danger">{{$purchase->total-$purchase->paid}}</span>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Status</th>
                                    <td>{{ \Illuminate\Support\Facades\Config::get('common.status')[$purchase->status] }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Quantity</th>
                                    <th>Received Quantity</th>
                                    <th>Unit Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($purchase->purchaseDetails as $item)
                                    <tr>
                                        <td>{{$item->material->name}}</td>
                                        <td>{{$item->quantity}}</td>
                                        <td>{{$item->received_quantity}}</td>
                                        <td>{{$item->unit_price}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
            <!-- END BORDERED TABLE PORTLET-->
        </div>
    </div>
@stop