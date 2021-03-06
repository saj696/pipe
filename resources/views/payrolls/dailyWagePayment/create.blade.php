@extends('layouts.app')

@section('content')
    <div class="portlet box blue-hoki">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-coffee"></i>Wage Payment
            </div>
            <div>
                <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                   href="{{ url('/daily_wage_payment' )}}">Back</a>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    {{ Form::open(['url'=>'daily_wage_payment']) }}
                    @include('payrolls.dailyWagePayment.form', ['submitText'=>'Payment Now'])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

@endsection