@extends('layouts.app')
@section('content')

    <div class="portlet box green ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i> New Purchase
            </div>
            <div>
                <a style="margin: 12px; padding: 5px;" class="label label-danger pull-right"
                   href="{{ url('/purchases' )}}">Back</a>
                <a style="margin: 12px; padding: 5px;" class="label label-warning pull-right"
                   href="{{ url('/suppliers/create' )}}">Add Supplier</a>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    {{ Form::open(['url'=>'purchases']) }}
                    @include('purchases.form', ['submitText'=>'Save'])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
