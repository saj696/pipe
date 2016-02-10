@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Users
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/users/create' )}}">New</a>
                    </div>
                </div>
                <?php
//                echo '<pre>';
//                print_r(Config::get('common.name'));
//                echo '</pre>';
                ?>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        Username
                                    </th>
                                    <th>
                                        email
                                    </th>
                                    <th>
                                        Name
                                    </th>
                                    <th>
                                        User Group
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
                            @if(sizeof($users)>0)
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    {{ $user->username }}
                                </td>
                                <td>
                                    {{ $user->email }}
                                </td>
                                <td>
                                    {{ $user->name_en }}
                                </td>
                                <td>
                                    {{ $user->userGroup->name_en }}
                                </td>
                                <td>
                                    {{ $user->status==1?'Active':'Inactive' }}
                                </td>
                                <td>
                                    <a class="label label-danger" href="{{ url('/users/'.$user->id.'/edit' )}}">Edit</a>
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
                    <div class="pagination"> {{ $users->links() }} </div>
                </div>
            </div>
        </div>
    </div>
@stop
