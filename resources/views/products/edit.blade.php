@extends('layouts.app')
@section('content')
    <div class="portlet box green ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i> Edit: {{ $product->title }}
            </div>
            <div>
                <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/products' )}}">Back</a>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    {{ Form::model($product, ['method'=>'PATCH','action'=>['Setup\ProductsController@update', $product->id]]) }}
                    @include('products.form', ['submitText'=>'Update'])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
