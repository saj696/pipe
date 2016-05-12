@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Material Stock Initialization
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
                                    Type
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(sizeof($materials)>0)
                                @foreach($materials as $material)
                                    <tr>
                                        <td>
                                            {{ $material->name }}
                                        </td>
                                        <td>
                                            {{ $types[$material->type] }}
                                        </td>
                                        <td>
                                            <a class="label label-danger" href="{{ url('/material_stock_initializations/'.$material->id.'/edit' )}}">Initialize</a>
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
                    <div class="pagination"> {{ $materials->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
