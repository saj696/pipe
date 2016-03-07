@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green-seagreen">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Sales Report
                    </div>
                </div>
                <div class="portlet-body form">
                    <div class="form-horizontal" role="form">
                        <div class="form-body">
                            {{ Form::open() }}

                            <div class="form-group">
                                {{ Form::label('workspace_id', 'Workspace', ['class'=>'col-md-3 control-label']) }}
                                <div class="col-md-7{{ $errors->has('workspace_id') ? ' has-error' : '' }}">
                                    {{ Form::select('workspace_id', $workspace, null, ['class'=>'form-control','id'=>'workspace_id','placeholder'=>'Select']) }}
                                    @if ($errors->has('workspace_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('workspace_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3">From Date</label>
                                <div class="col-md-3">
                                    <input name="from_date" type="text" value="{{ date('d-m-Y') }}" size="16"
                                           class="form-control from_datepicker">
                                </div>

                                <label class="control-label col-md-1">To Date</label>
                                <div class="col-md-3">
                                    <input name="to_date" type="text" value="{{ date('d-m-Y') }}" size="16"
                                           class="form-control to_datepicker">
                                </div>
                            </div>

                            <div class="form-group">
                                {{ Form::label('sales_type', 'Sales Type', ['class'=>'col-md-3 control-label']) }}
                                <div class="col-md-7{{ $errors->has('sales_type') ? ' has-error' : '' }}">
                                    {{ Form::select('sales_type', array_flip(Config::get('report.sales_type')), null, ['class'=>'form-control','id'=>'sales_customer_type']) }}
                                    @if ($errors->has('sales_type'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('sales_type') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="text-center col-md-12">
                                        {{ Form::submit('Search', ['class'=>'btn btn-circle green','id'=>'submit']) }}
                                    </div>
                                </div>
                            </div>

                            {{ Form::close() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.from_datepicker').datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd-mm-yy',
                onClose: function (selectedDate) {
                    $(".to_datepicker").datepicker("option", "minDate", selectedDate);
                }
            });

            $('.to_datepicker').datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd-mm-yy',
                onClose: function (selectedDate) {
                    $(".from_datepicker").datepicker("option", "minDate", selectedDate);
                }
            });

        $(document).on('click','#submit', function(e){

            e.preventDefault();
            $.ajax({
                type:'POST',
                url: '{{ route('ajax.sales_report') }}',
                data: $('form').serialize(),
                success: function (data, status) {

                },
                error: function(xhr, desc, err)
                {

                }
            })
        });

        });
    </script>

@stop

