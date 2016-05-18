@extends('layouts.app')
@section('content')

    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i> New Payment Receive
            </div>
            <div>
                <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/receive_payments' )}}">Back</a>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    {{ Form::open(['url'=>'receive_payments']) }}
                    @include('receivePayment.form', ['submitText'=>'Save'])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
