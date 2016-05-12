@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Product Stock Initialization
                    </div>
                </div>

                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Type
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(sizeof($products)>0)
                                @foreach($products as $product)
                                    <tr>
                                        <td>
                                            {{ $product->title }}
                                        </td>
                                        <td>
                                            {{ $types[$product->product_type_id] }}
                                        </td>
                                        <td>
                                            <a class="label label-danger" href="{{ url('/product_stock_initializations/'.$product->id.'/edit' )}}">Initialize</a>
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
                    <div class="pagination"> {{ $products->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
