@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green-seagreen">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Trial Balance
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

                            <div class="form-actions">
                                <div class="row">
                                    <div class="text-center col-md-12">
                                        {{ Form::submit('Search', ['class'=>'btn btn-circle green-seagreen','id'=>'submit']) }}
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
            $(document).on('click', '#submit', function (e) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('ajax.trial_balance') }}',
                    data: $('form').serialize(),
                    success: function (data, status) {

                        $('.col-md-7').removeClass('has-error');
                        $('.error').empty();
                    },
                    error: function (data) {
                        var errors = $.parseJSON(data.responseText);

                        $.each(errors, function (index, value) {
                            console.log(value);
                            var obj = $('#workspace_id');
                            console.log(obj)
                            obj.closest('.form-group').find('.col-md-7').addClass('has-error');
                            var html = '<span class="help-block">' +
                                    '<strong>' + value + '</strong>' +
                                    '</span>';
                            obj.closest('.form-group').find('.error').html(html)

                        });
                    }
                });

                e.preventDefault();
            });

        });
    </script>

@stop
