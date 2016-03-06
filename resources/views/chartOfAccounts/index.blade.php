@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Chart Of Accounts
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="{{ url('/charts/create' )}}">New</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row-details">
                        {!! $html !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
