<?php
/**
 * Created by PhpStorm.
 * User: Rana
 * Date: 14-02-16
 * Time: 15.36
 */
?>

@extends('layouts.app')
@section('content')

    <div class="portlet box green ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i> Edit Product & Service Provider
            </div>
            <div>
                <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="{{ url('/providers' )}}">Back</a>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    {{ Form::model($provider, ['method'=>'PATCH','action'=>['Setup\ProvidersController@update', $provider->id],'files'=>true]) }}
                    @include('providers.form', ['submitText'=>'Update'])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop

