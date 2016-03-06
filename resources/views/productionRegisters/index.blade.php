@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Daily Production Registers
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/productionRegisters/create') }}">New</a>
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
                                    Product
                                </th>
                                <th>
                                    production
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
                            @if(sizeof($productionRegisters)>0)
                                @foreach($productionRegisters as $productionRegister)
                                    <tr>
                                        <td>
                                            {{ $productionRegister->date }}
                                        </td>
                                        <td>
                                            {{ $productionRegister->product->title }}
                                        </td>
                                        <td>
                                            {{ $productionRegister->production }}
                                        </td>
                                        <td>
                                            {{ $status[$productionRegister->status] }}
                                        </td>
                                        <td>
                                            <a class="label label-danger"
                                               href="{{ url('/productionRegisters/'.$productionRegister->id.'/edit' )}}">Edit</a>
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
                    <div class="pagination"> {{ $productionRegisters->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
