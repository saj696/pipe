@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Discarded Materials
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/discarded_stock/create') }}">New</a>
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
                                    Discarded Material
                                </th>
                                <th>
                                    Quantity
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(sizeof($discardedMaterialStocks)>0)
                                @foreach($discardedMaterialStocks as $discardedMaterialStock)
                                    <tr>
                                        <td>
                                            {{ $discardedMaterialStock->date }}
                                        </td>
                                        <td>
                                            {{ $materials[$discardedMaterialStock->material_id] }}
                                        </td>
                                        <td>
                                            {{ $discardedMaterialStock->quantity }}
                                        </td>
                                        <td>
                                            <a class="label label-danger"
                                               href="{{ url('/discarded_stock/'.$discardedMaterialStock->id.'/edit' )}}">Edit</a>
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
                    <div class="pagination"> {{ $discardedMaterialStocks->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
