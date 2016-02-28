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
                        {{ Form::label('extra_hours', 'Extra Hours', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::text('extra_hours',$salary->extra_hours,['class'=>'form-control','data-hourly_rate'=>$salary->employee->designation->hourly_rate]) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('extra_salary', 'Extra Salary', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::text('extra_salary',null,['class'=>'form-control','readonly']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('cut', 'Cut', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::text('cut',$salary->cut, ['class'=>'form-control']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('bonus', 'Bonus', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::text('bonus', $salary->bonus, ['class'=>'form-control']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('net', 'Net Salary', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-7">
                            {{ Form::text('net',$salary->net, ['class'=>'form-control','readonly']) }}
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
            $(document).on('change', '#cut', function () {
                $('#net').val(calculate());
            });

            $(document).on('change', '#extra_hours', function () {
                var over_time = parseFloat($(this).val());
                var over_time_rate = parseFloat($(this).data('hourly_rate'));
                var net = parseFloat($('#net').val());
                if ((over_time * over_time_rate) > 0) {
                    var total = over_time * over_time_rate;
                    $('#extra_salary').val(total)
                }
                $('#net').val(calculate());
            });

            $(document).on('change', '#bonus', function () {
                $('#net').val(calculate());
            });
        });

        function calculate()
        {
            var extra_salary = parseFloat($('#extra_salary').val());
            if(isNaN(extra_salary))
                    extra_salary=0;
            var cut = parseFloat($('#cut').val());
            var bonus = parseFloat($('#bonus').val());
            var salary = parseFloat($('#salary').val());

            return ((salary+extra_salary+bonus)-cut)
        }
    </script>
@stop