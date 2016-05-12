@extends('layouts.app')
@section('content')
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i> Initialize: {{ $material->name }}
            </div>
            <div>
                <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/material_stock_initializations' )}}">Back</a>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    {{ Form::model($material, ['method'=>'PATCH','action'=>['Setup\MaterialStockInitializationsController@update', $material->id]]) }}
                    @include('materialStockInitializations.form', ['submitText'=>'Ok'])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
