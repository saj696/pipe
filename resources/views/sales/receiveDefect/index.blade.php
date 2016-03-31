@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Defect Receive
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/receive_defect/create' )}}">New Defect Receive</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>
                                    Customer
                                </th>

                                <th>
                                    Workspace
                                </th>
                                <th>
                                    Receive Date
                                </th>
                                <th>
                                    Total Price
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>

                            @if(sizeof($defects)>0)
                                @foreach($defects as $defect)
                                    <tr>
                                        <td>{{ \App\Helpers\CommonHelper::getCustomerName($defect->customer_id,$defect->customer_type) }}</td>
                                        <td>{{ $defect->workspaces->name }}</td>
                                        <td>{{ date('d-m-Y',$defect->date) }}</td>
                                        <td>{{ $defect->total}}</td>
                                        <td>
                                            <a class="label label-danger"
                                               href="{{ url('/receive_defect/'.$defect->id.'/edit' )}}">Edit</a>
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
                </div>
            </div>
        </div>
    </div>

@endsection