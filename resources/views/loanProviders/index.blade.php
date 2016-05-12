@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Loan Providers
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/loan_providers/create' )}}">New</a>
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
                                    Mobile
                                </th>
                                <th>
                                    Address
                                </th>
                                <th>
                                    Company Name
                                </th>
                                <th>
                                    Company Address
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
                            @if(sizeof($providers)>0)
                                @foreach($providers as $provider)
                                    <tr>
                                        <td>
                                            {{ $provider->name }}
                                        </td>
                                        <td>
                                            {{ $provider->mobile }}
                                        </td>
                                        <td>
                                            {{ $provider->address }}
                                        </td>
                                        <td>
                                            {{ $provider->company_name }}
                                        </td>
                                        <td>
                                            {{ $provider->company_address }}
                                        </td>
                                        <td>
                                            {{ ($provider->status==1)? "Active" : "In-Active" }}
                                        </td>
                                        <td>
                                            <a class="label label-danger" href="{{ url('/loan_providers/'.$provider->id.'/edit' )}}">Edit</a>
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
                    <div class="pagination"> {{ $providers->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
