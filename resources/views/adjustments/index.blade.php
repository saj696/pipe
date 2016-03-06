@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Adjustments
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/adjustments/create' )}}">New</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>
                                    Year
                                </th>
                                <th>
                                    Account
                                </th>
                                <th>
                                    Amount
                                </th>
                                <th>
                                    Adjustment To
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(sizeof($adjustments)>0)
                                @foreach($adjustments as $adjustment)
                                    <tr>
                                        <td>
                                            {{ $adjustment->year }}
                                        </td>
                                        <td>
                                            {{ $adjustment->account_from }}
                                        </td>
                                        <td>
                                            {{ $adjustment->amount }}
                                        </td>
                                        <td>
                                            {{ $adjustment->account_to }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center danger">No Data Found</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination"> {{ $adjustments->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
