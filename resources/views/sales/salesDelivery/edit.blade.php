@extends('layouts.app')
@section('content')

    <div class="portlet box green ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i>Sales Delivery
            </div>
            <div>
                <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                   href="{{ url('/salesDelivery' )}}">Back</a>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    {{ Form::open(['url'=>'salesDelivery']) }}
                    @include('sales.salesDelivery.form', ['submitText'=>'Deliver'])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
