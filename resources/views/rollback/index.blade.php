@extends('layouts.app')
@section('content')

    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i>Rollback Accounts
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    {{ Form::open(['url'=>'rollback']) }}
                    <div class="form-group">
                        {{ Form::label('workspace_id', 'Workspace', ['class'=>'col-md-3 control-label']) }}
                        <div class="col-md-6">
                            {{ Form::select('workspace_id',$workspaces , null, ['class'=>'form-control','id'=>'workspace_id','placeholder'=>'Select']) }}
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="col-md-12 text-center">
                            {{ Form::submit('Rollback', ['class'=>'btn btn-circle red']) }}
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <script>
        $('form').submit(function (event)
        {
            var workspace = $('#workspace_id').val();
            if(workspace>0)
            {
                var x = confirm("Are you sure you want to rollback this workspace accounts?");
            }
            else
            {
                var x = confirm("Are you sure you want to rollback?");
            }

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
