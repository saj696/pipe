@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Workspace Account Closing
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover" style="border-top: 0px;">
                            <tbody>
                            <tr>
                                <td>
                                    {{ Form::open(['url'=>'workspace_closing']) }}
                                    {!! csrf_field() !!}
                                    <div class="form-actions">
                                        <div class="col-md-12 text-center">
                                            {{ Form::submit('Account Close', ['class'=>'btn btn-circle green']) }}
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('form').submit(function (event)
        {
            var x = confirm("Are you sure you want to close your workspace for this year?");

            if (x) {
                return true;
            }
            else {
                event.preventDefault();
                return false;
            }
        });
    </script>
@stop
