@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green-seagreen">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Transaction Statement
                    </div>
                </div>
                <div class="portlet-body form">
                    <div class="form-horizontal" role="form">
                        <div class="form-body">
                            {{ Form::open() }}

                            <div class="form-group">
                                {{ Form::label('statement_for', 'Statement For', ['class'=>'col-md-3 control-label']) }}
                                <div class="col-md-7">
                                    {{ Form::select('statement_for', Config::get('common.transaction_statement_type'), null, ['class'=>'form-control','id'=>'statement_for','placeholder'=>'Select','required']) }}
                                    <div class="error"></div>
                                </div>
                            </div>

                            <div class="person_div" style="display:none;">
                            </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="text-center col-md-12">
                                        {{ Form::submit('Report', ['class'=>'btn btn-circle green-seagreen','id'=>'submit']) }}
                                    </div>
                                </div>
                            </div>

                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12" id="load_view">

        </div>
    </div>

    <script>
        $(document).ready(function () {
            $(document).on('change', '#statement_for', function () {
                var type = $(this).val();

                if (type > 0) {
                    var url = "";
                    if (type == 2) {
                        url = "{{ route('ajax.supplier_select') }}";
                    }
                    else if (type == 3) {
                        url = "{{ route('ajax.customer_select') }}";
                    }

                    $.ajax({
                        url: url,
                        type: 'POST',
                        dataType: "JSON",
                        success: function (data, status) {
                            $('.person_div').empty();
                            $('.person_div').html(data);
                            $('.person_div').show();

                            $('.employee_customer_supplier').attr('name', 'person_id');
                            $('.employee_customer_supplier').attr('id', 'person_id');
                            $('.select2me').select2();
                        },
                        error: function (xhr, desc, err) {
                            console.log("error");
                        }
                    });
                }
                else {
                    $('.person_div').empty();
                    $('.person_div').hide();
                }
            });

            $(document).on('click', '#submit', function (e) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('ajax.transaction_statements_report') }}',
                    data: $('form').serialize(),
                    success: function (data, status) {

                        $('.col-md-7').removeClass('has-error');
                        $('.error').empty();
                        $('#load_view').html(data);
                    },
                    error: function (data) {
                        var errors = $.parseJSON(data.responseText);

                        $('#load_view').html('');
                        $('.col-md-7').removeClass('has-error');
                        $('.error').empty();

                        $.each(errors, function (index, value) {
                            console.log(index);
                            var obj = $('#'+index);
                            console.log(obj);
                            obj.closest('.form-group').find('.col-md-7').addClass('has-error');
                            var html = '<span class="help-block">' +
                                    '<strong>' + value + '</strong>' +
                                    '</span>';
                            obj.closest('.form-group').find('.error').html(html);
                        });
                    }
                });

                e.preventDefault();
            });
        });
    </script>

@stop

