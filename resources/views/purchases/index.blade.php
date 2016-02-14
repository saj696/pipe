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
                                        Title
                                    </th>
                                    <th>
                                        purchase Type
                                    </th>
                                    <th>
                                        Color
                                    </th>
                                    <th>
                                        Wholesale Price
                                    </th>
                                    <th>
                                        Retail Price
                                    </th>
                                    <th>
                                        Diameter
                                    </th>
                                    <th>
                                        Weight
                                    </th>
                                    <th>
                                        Length
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
                                <td>{{ $purchase->title }}</td>
                                <td>{{ $purchase->purchaseTypes->title }}</td>
                                <td>{{ $purchase->materials->name }}</td>
                                <td>{{ $purchase->wholesale_price}}</td>
                                <td>{{ $purchase->retail_price}}</td>
                                <td>{{ $purchase->diameter }}</td>
                                <td>{{ $purchase->weight }}</td>
                                <td>{{ $purchase->length }}</td>
                                <td>{{ \Illuminate\Support\Facades\Config::get('common.status')[$purchase->status] }}</td>
                                <td>
                                    <a class="label label-danger" href="{{ url('/purchases/'.$purchase->id.'/edit' )}}">Edit</a>
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
                    {{--<div class="pagination"> {{ $tasks->links() }} </div>--}}
                </div>
            </div>
        </div>
    </div>
@stop
