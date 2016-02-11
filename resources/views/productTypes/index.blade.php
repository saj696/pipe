@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Product Types
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/product_types/create' )}}">New</a>
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
                                        Created Time
                                    </th>
                                    <th>
                                        Updated Time
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

                            @if(sizeof($product_types)>0)
                            @foreach($product_types as $product_type)
                            <tr>
                                <td>{{ $product_type->title }}</td>
                                <td>{{ $product_type->created_at ? date('d-m-Y  h:i:s A',$product_type->created_at): '' }}</td>
                                <td>{{ $product_type->updated_by ? date('d-m-Y  h:i:s A',$product_type->updated_by): '' }}</td>
                                <td>{{ \Illuminate\Support\Facades\Config::get('common.status')[$product_type->status] }}</td>
                                <td>
                                    <a class="label label-danger" href="{{ url('/product_types/'.$product_type->id.'/edit' )}}">Edit</a>
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
