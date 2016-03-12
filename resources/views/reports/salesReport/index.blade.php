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
                                <div class="col-md-7">
                                    {{ Form::select('workspace_id', $workspace, null, ['class'=>'form-control','id'=>'workspace_id','placeholder'=>'Select','required']) }}
                                    <div class="error"></div>
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
                                {{ Form::label('customer_type', 'Customer Type', ['class'=>'col-md-3 control-label']) }}
                                <div class="col-md-7">
                                    <select name="customer_type" id="customer_type" class="form-control">
                                        <option selected="selected" value="0">All</option>
                                        @foreach(Config::get('common.sales_customer_type') as $key=>$value )
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="customer_id">

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
        <div class="col-md-12" id="load_report">

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
                    $(".from_datepicker").datepicker("option", "maxDate", selectedDate);
                }
            });

            $(document).on('change', '#customer_type', function () {
                var type = parseInt($(this).val());
                var url = "";
                if (type == 2) {
                    url = "{{ route('ajax.supplier_select') }}";
                } else if (type == 1) {
                    url = "{{ route('ajax.employee_select') }}";
                } else if (type == 3) {
                    url = "{{ route('ajax.customer_select') }}";
                } else {
                    $('.customer_id').empty();
                }

                if (type > 0) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        dataType: "JSON",
                        success: function (data, status) {
                            $('.customer_id').empty();
                            $('.customer_id').html(data);

                            $('.select2me').select2();
                        },
                        error: function (xhr, desc, err) {
                            console.log("error");
                        }
                    });
                }
            });


            $(document).on('click', '#submit', function (e) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('ajax.sales_report') }}',
                    data: $('form').serialize(),
                    success: function (data, status) {
                        $('#load_report').html(data)
                        $('.col-md-7').removeClass('has-error')
                        $('.error').empty()

                    },
                    error: function (data) {
                        var errors = $.parseJSON(data.responseText);

                        $('#load_view').html('');
                        $('.col-md-7').removeClass('has-error');
                        $('.error').empty();

                        $.each(errors, function (index, value) {
                            console.log(index);
                            var obj = $('#' + index);
                            console.log(obj);
                            obj.closest('.form-group').find('.col-md-7').addClass('has-error');
                            var html = '<span class="help-block">' +
                                    '<strong>' + value + '</strong>' +
                                    '</span>';
                            obj.closest('.form-group').find('.error').html(html);
                        });
                    }
                })

                e.preventDefault();
            });

        });
    </script>

@stop

