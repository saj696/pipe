@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Purchases
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/purchases/create' )}}">New purchase</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        Supplier
                                    </th>
                                    <th>
                                        Purchase Date
                                    </th>
                                    <th>
                                        No of Items
                                    </th>
                                    <th>
                                        Transportation Cost
                                    </th>
                                    <th>
                                        Total
                                    </th>
                                    <th>
                                        Paid
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

                            @if(sizeof($purchases)>0)
                            @foreach($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->supplier->company_name }}</td>
                                <td>{{ $purchase->purchase_date }}</td>
                                <td>{{ $purchase->purchaseDetails->count() }}</td>
                                <td>{{ $purchase->transportation_cost }}</td>
                                <td>{{ $purchase->total }}</td>
                                <td>{{ $purchase->paid }}</td>
                                <td>{{ \Illuminate\Support\Facades\Config::get('common.status')[$purchase->status] }}</td>
                                <td>
                                    <a class="label label-danger" href="{{ url('/purchases/'.$purchase->id.'/edit' )}}">Edit</a>
                                    <a class="label label-info" href="{{ url('/purchases/'.$purchase->id )}}">View</a>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="8" class="text-center danger">No Data Found</td>
                            </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination"> {{ $purchases->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
