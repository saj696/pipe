@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Banks
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/banks/create' )}}">New</a>
                    </div>
                </div>

                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>
                                    Bank Name
                                </th>
                                <th>
                                    Account No.
                                </th>
                                <th>
                                    Type
                                </th>
                                <th>
                                    Balance
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
                            @if(sizeof($banks)>0)
                                @foreach($banks as $bank)
                                    <tr>
                                        <td>
                                            {{ $bank->name }}
                                        </td>
                                        <td>
                                            {{ $bank->account_no }}
                                        </td>
                                        <td>
                                            {{ Config::get('common.account_type')[$bank->account_type] }}
                                        </td>
                                        <td>
                                            {{ $bank->balance }}
                                        </td>
                                        <td>
                                            {{ ($bank->status==1)? "Active" : "In-Active" }}
                                        </td>
                                        <td>
                                            <a class="label label-danger" href="{{ url('/banks/'.$bank->id.'/edit' )}}">Edit</a>
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
                    <div class="pagination"> {{ $banks->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
