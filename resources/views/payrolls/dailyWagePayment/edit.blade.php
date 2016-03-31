@extends('layouts.app')
@section('content')
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i> Edit Wage: {{ $wage->employee->name }}
            </div>
            <div>
                <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                   href="{{ url('/daily_wage_payment' )}}">Back</a>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    {{ Form::open(['method'=>'PATCH','action'=>['Payroll\DailyWagePaymentController@update', $wage->id]]) }}
                    @include('payrolls.dailyWagePayment.editForm', ['submitText'=>'Update'])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop