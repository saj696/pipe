@extends('layouts.app')
@section('content')
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i> Initialize: {{ $product->title }}
            </div>
            <div>
                <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/product_stock_initializations' )}}">Back</a>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    {{ Form::model($product, ['method'=>'PATCH','action'=>['Setup\ProductStockInitializationsController@update', $product->id]]) }}
                    @include('productStockInitializations.form', ['submitText'=>'Ok'])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
