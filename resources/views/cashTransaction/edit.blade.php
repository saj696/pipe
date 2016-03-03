@extends('layouts.app')
@section('content')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i> Edit Transaction
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
                    {{ Form::model($recorder, ['method'=>'PATCH','action'=>['Account\TransactionRecordersController@update', $recorder->id]]) }}
                    @include('transactionRecorders.form', ['submitText'=>'Update'])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
