@extends('layouts.app')
@section('content')

    <div class="portlet box green ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i>Payment Salary
            </div>
            <div>
                <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                   href="{{ url('salary_payment' )}}">Back</a>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    {{ Form::open(['method'=>'PATCH','action'=>['Payroll\SalaryPaymentController@update', $salaryPayment->id]]) }}
                    {{--<input type="hidden" name="id" value="{{ $salaryPayment }}">--}}

                    <div class="form-group">
                        {{ Form::label('name', 'Name', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::text('name',$salaryPayment->employee->name,['class'=>'form-control','disabled']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('year', 'Year', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::text('year',$salaryPayment->year,['class'=>'form-control','disabled']) }}
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('month', 'Month', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::text('month',date('F', mktime(0, 0, 0, $salaryPayment->month)), ['class'=>'form-control','disabled']) }}
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('salary', 'Salary', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::text('salary',$salaryPayment->amount, ['class'=>'form-control']) }}
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="row">
                            <div class="text-center col-md-12">
                                {{ Form::submit('Update', ['class'=>'btn btn-circle green']) }}
                            </div>
                        </div>
                    </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        $(document).ready(function () {

        });

    </script>
@stop