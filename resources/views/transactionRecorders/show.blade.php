@extends('layouts.app')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Module Detail
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/modules' )}}">Back</a>
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
                                    Component
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
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    {{ $module->name_en }}
                                </td>
                                <td>
                                    <p>{{ $module->component->name_en }}</p>
                                </td>
                                <td>
                                    <p>{{ $module->description }}</p>
                                </td>
                                <td>
                                    {{ $module->icon }}
                                </td>
                                <td>
                                    {{ $module->ordering }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END BORDERED TABLE PORTLET-->
        </div>
    </div>
@stop