@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green-seagreen">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Payroll Report
                    </div>
                </div>
                <div class="portlet-body form">
                    <div class="form-horizontal" role="form">
                        <div class="form-body">
                            {{ Form::open() }}

                            <div class="form-group">
                                {{ Form::label('workspace_id', 'Workspace', ['class'=>'col-md-3 control-label']) }}
                                <div class="col-md-7">
                                    <select name="workspace_id" class='form-control' id='workspace_id' required>
                                        <option selected="selected" value="0">All</option>
                                        @foreach($workspaces as $key=>$workspace)
                                            <option value="{{ $key }}">{{ $workspace }}</option>
                                        @endforeach
                                    </select>
                                    <div class="error"></div>
                                </div>
                            </div>

                            <div class="form-group">
                                {{ Form::label('month', 'Month', ['class'=>'col-md-3 control-label']) }}
                                <div class="col-md-7">
                                    {{ Form::select('month', array_flip(Config::get('common.month')), null, ['class'=>'form-control','id'=>'month','placeholder'=>'Select','required']) }}
                                    <div class="error"></div>
                                </div>
                            </div>

                            <div class="form-group">
                                {{ Form::label('year', 'Year', ['class'=>'col-md-3 control-label']) }}
                                <div class="col-md-7">
                                    {{ Form::select('year', $years, null, ['class'=>'form-control','id'=>'year','placeholder'=>'Select','required']) }}
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
        <div class="col-lg-12" id="load_view">

        </div>
    </div>

    <script>
        $(document).ready(function () {
            $(document).on('click', '#submit', function (e) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('ajax.payroll_report') }}',
                    data: $('form').serialize(),
                    success: function (data, status) {
                        console.log(data)

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
                            var obj = $('#' + index);
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

@endsection