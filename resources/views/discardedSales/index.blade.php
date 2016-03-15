@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Discarded Sales
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/discarded_sale/create') }}">New</a>
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
                                    Amount
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(sizeof($discardedSales)>0)
                                @foreach($discardedSales as $discardedSale)
                                    <tr>
                                        <td>
                                            {{ $discardedSale->date }}
                                        </td>
                                        <td>
                                            {{ $discardedSale->material->name }}
                                        </td>
                                        <td>
                                            {{ $discardedSale->quantity }}
                                        </td>
                                        <td>
                                            {{ $discardedSale->amount }}
                                        </td>
                                        <td>
                                            <a class="label label-danger"
                                               href="{{ url('/discarded_sale/'.$discardedSale->id.'/edit' )}}">Edit</a>
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
                    <div class="pagination"> {{ $discardedSales->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
