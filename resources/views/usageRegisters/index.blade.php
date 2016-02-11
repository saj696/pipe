@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Daily Usage Registers
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/usageRegisters/create') }}">New</a>
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
                                        Material
                                    </th>
                                    <th>
                                        Usage
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
                            @if(sizeof($usageRegisters)>0)
                                @foreach($usageRegisters as $usageRegister)
                                    <tr>
                                        <td>
                                            {{ $usageRegister->date }}
                                        </td>
                                        <td>
                                            {{ $usageRegister->material_id }}
                                        </td>
                                        <td>
                                            {{ $usageRegister->usage }}
                                        </td>
                                        <td>
                                            {{ $status[$usageRegister->status] }}
                                        </td>
                                        <td>
                                            <a class="label label-danger" href="{{ url('/usageRegisters/'.$usageRegister->id.'/edit' )}}">Edit</a>
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
                    <div class="pagination"> {{ $usageRegisters->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
