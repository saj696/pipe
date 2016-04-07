@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Transaction Recorders
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/recorders/create' )}}">New</a>
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
                                    Account
                                </th>
                                <th>
                                    Total Amount
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
                            @if(sizeof($recorders)>0)
                                @foreach($recorders as $recorder)
                                    <tr>
                                        <td>
                                            {{ $recorder->date }}
                                        </td>
                                        <td>
                                            {{ $accounts[$recorder->account_code] }}
                                        </td>
                                        <td>
                                            {{ isset($recorder->total_amount)?$recorder->total_amount:'N/A' }}
                                        </td>
                                        <td>
                                            {{ $recorder->amount }}
                                        </td>
                                        <td>
                                            <a class="label label-danger"
                                               href="{{ url('/recorders/'.$recorder->id.'/edit' )}}">Edit</a>
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
                    <div class="pagination"> {{ $recorders->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
