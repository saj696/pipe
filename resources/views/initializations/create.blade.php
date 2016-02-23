@extends('layouts.app')
@section('content')

    <div class="portlet box green ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i> New Component
            </div>
            <div>
                <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/components' )}}">Back</a>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    {{ Form::open(['url'=>'components']) }}
                    @include('components.form', ['submitText'=>'Add'])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop