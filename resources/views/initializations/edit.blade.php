@extends('layouts.app')
@section('content')
    <div class="portlet box green ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i> Account Initialization
            </div>
            <div class="tools">
                <a href="" class="collapse">
                </a>
                <a href="#portlet-config" data-toggle="modal" class="config">
                </a>
                <a href="" class="reload">
                </a>
                <a href="" class="remove">
                </a>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    {{ Form::open(['method'=>'PATCH','action'=>['Account\InitializationsController@update', $id]]) }}
                        <table class="table table-bordered">
                            <tr>
                                <th>Account</th>
                                <th>Opening Balance</th>
                            </tr>
                            @foreach($accounts as $account)
                                <tr>
                                    <td>{{ $account->name }}</td>
                                    <td><input type="text" name="balance[{{ $account->code }}]" class="form-control" value="" /></td>
                                </tr>
                            @endforeach
                                <tr>
                                    <td colspan="20" class="text-center">
                                        <div class="row">
                                            <div class="text-center col-md-12">
                                                {{ Form::submit('Save', ['class'=>'btn btn-circle green']) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                        </table>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
