@extends('layouts.app')
@section('content')

    <div class="portlet box green ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i>Edit Salary
            </div>
            <div>
                <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                   href="{{ url('salary_generator' )}}">Back</a>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    {{ Form::open(['method'=>'PATCH','action'=>['Payroll\SalaryGeneratorController@update', $salary->id]]) }}
                    {{--<input type="hidden" name="id" value="{{ $salary }}">--}}

                    <div class="form-group">
                        {{ Form::label('name', 'Name', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::text('name',$salary->employee->name,['class'=>'form-control','disabled']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('year', 'Year', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::text('year',$salary->year,['class'=>'form-control','disabled']) }}
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('month', 'Month', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::text('month',date('F', mktime(0, 0, 0, $salary->month)), ['class'=>'form-control','disabled']) }}
                        </div>
                    </div>
                    <div class="form-group">
                        {{ Form::label('salary', 'Salary', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::text('salary',$salary->salary, ['class'=>'form-control','disabled']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('cut', 'Cut', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::number('cut',$salary->cut, ['class'=>'form-control','min'=>0,'step'=>0.01]) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('net', 'Net Salary', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::number('net',$salary->net, ['class'=>'form-control','readonly','min'=>$salary->net_paid, 'step'=>0.01]) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('over_time', 'Overtime', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::number('over_time',$salary->over_time,['class'=>'form-control','data-hourly_rate'=>$salary->employee->designation->hourly_rate, 'min'=>0, 'step'=>0.01]) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('overtime_amount', 'Overtime Amount', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::number('overtime_amount',$salary->over_time*$salary->employee->designation->hourly_rate,['class'=>'form-control','readonly','min'=>$salary->over_time_paid]) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('bonus', 'Bonus', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::number('bonus', $salary->bonus, ['class'=>'form-control','min'=>$salary->bonus_paid,'step'=>0.01]) }}
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
            $(document).on('keyup', '#cut', function () {
                var cut = parseFloat($(this).val());
                var salary = parseFloat($('#salary').val());
                if (isNaN(cut)) {
                    cut = 0;
                }
                $('#net').val(salary - cut);
            });

            $(document).on('keyup', '#over_time', function () {
                var over_time = parseFloat($(this).val());
                var over_time_rate = parseFloat($(this).data('hourly_rate'));
                if (isNaN(over_time)) {
                    over_time = 0;
                }
                if (isNaN(over_time_rate)) {
                    over_time_rate = 0;
                }
                var total = over_time * over_time_rate;
                $('#overtime_amount').val(total.toFixed(2))
            });

            $(document).on('submit','form', function(e){
                var over_time = parseFloat($('#over_time').val());
                var over_time_rate = parseFloat($('#over_time').data('hourly_rate'));
                var over_time_paid= '{{ $salary->over_time_paid }}'

                if((over_time*over_time_rate) < over_time_paid ){
                    e.preventDefault();
                    alert('Overtime amount not less than '+over_time_paid);
                }
            })
        });

    </script>
@stop