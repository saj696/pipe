@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green-seagreen">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Daily Cash Flow
                    </div>
                </div>
                <div class="portlet-body form">
                    <div class="form-horizontal" role="form">
                        <div class="form-body">
                            {{ Form::open() }}

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
            $(document).on('click', '#submit', function (e) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('ajax.cash_flow_report') }}',
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

