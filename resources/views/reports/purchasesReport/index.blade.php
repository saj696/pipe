@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green-seagreen">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Purchases Report
                    </div>
                </div>
                <div class="portlet-body form">
                    <div class="form-horizontal" role="form">
                        <div class="form-body">
                            {{ Form::open() }}

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
                                {{ Form::label('supplier_id', 'Supplier', ['class'=>'col-md-3 control-label']) }}
                                <div class="col-md-7">
                                    <select name="supplier_id" id="" class="form-control select2me">
                                        <option selected="selected" value="0">All</option>
                                        @foreach($suppliers as $key=>$value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                {{ Form::label('purchase_type', 'Purchases Type', ['class'=>'col-md-3 control-label']) }}
                                <div class="col-md-7{{ $errors->has('purchase_type') ? ' has-error' : '' }}">
                                    {{ Form::select('purchase_type', array_flip(Config::get('report.purchase_type')), null, ['class'=>'form-control','id'=>'']) }}
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
                    $(".from_datepicker").datepicker("option", "minDate", selectedDate);
                }
            });

            $(document).on('click', '#submit', function (e) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('ajax.purchase_report') }}',
                    data: $('form').serialize(),
                    success: function (data, status) {
                        $('#load_report').html(data)
                    },
                    error: function (data) {
                    }
                })

                e.preventDefault();
            });

        });
    </script>

@stop

