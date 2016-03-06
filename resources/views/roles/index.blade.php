@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>User Group Roles
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>
                                    {{--{{ trans('user.group_name') }}--}}
                                    Group Name
                                </th>
                                <th>
                                    Total Component
                                </th>
                                <th>
                                    Total Module
                                </th>
                                <th>
                                    Total Task
                                </th>
                                <th>
                                    Last Edit
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            @if(sizeof($groups)>0)
                                @foreach($groups as $group)
                                    <?php $detail = App\Helpers\UserHelper::getUserGroupRoleDetail($group->id); ?>
                                    <tr>
                                        <td>
                                            {{ $group->name_en }}
                                        </td>
                                        <td>
                                            {{ $detail->total_component }}
                                        </td>
                                        <td>
                                            {{ $detail->total_module }}
                                        </td>
                                        <td>
                                            {{ $detail->total_task }}
                                        </td>
                                        <td>
                                            {{ isset($detail->last_update_date)?date('Y-m-d',$detail->last_update_date):'Not Done' }}
                                        </td>
                                        <td>
                                            <a class="label label-danger"
                                               href="{{ url('/roles/'.$group->id.'/edit' )}}">Edit</a>
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
                    <div class="pagination"> {{ $groups->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
