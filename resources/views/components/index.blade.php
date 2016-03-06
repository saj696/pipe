@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Components
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/components/create' )}}">New</a>
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
                                    Description
                                </th>
                                <th>
                                    Icon
                                </th>
                                <th>
                                    Order
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(sizeof($components)>0)
                                @foreach($components as $component)
                                    <tr>
                                        <td>
                                            <a href="{{ url('/components', $component->id )}}">{{ $component->name_en }}</a>
                                        </td>
                                        <td>
                                            {{ str_limit($component->description, 50) }}
                                        </td>
                                        <td>
                                            {{ $component->icon }}
                                        </td>
                                        <td>
                                            {{ $component->ordering }}
                                        </td>
                                        <td>
                                            <a class="label label-danger"
                                               href="{{ url('/components/'.$component->id.'/edit' )}}">Edit</a>
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
                    <div class="pagination"> {{ $components->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
