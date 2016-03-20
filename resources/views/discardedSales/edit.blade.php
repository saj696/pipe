@extends('layouts.app')
@section('content')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i>Edit Discarded Sale
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
                    {{ Form::model($discardedSales, ['method'=>'PATCH','action'=>['Discarded\DiscardedMaterialSaleController@update', $discardedSales->id]]) }}
                    @include('discardedSales.form', ['submitText'=>'Update'])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
