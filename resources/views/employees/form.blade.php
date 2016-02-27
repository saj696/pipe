{!! csrf_field() !!}

<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
    {{ Form::label('name', 'Name', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('name', null,['class'=>'form-control']) }}
        @if ($errors->has('name'))
            <span class="help-block">
                <strong>{{ $errors->first('name') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
    {{ Form::label('mobile', 'Mobile', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('mobile', null,['class'=>'form-control']) }}
        @if ($errors->has('mobile'))
            <span class="help-block">
                <strong>{{ $errors->first('mobile') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
    {{ Form::label('email', 'Email', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('email', null,['class'=>'form-control']) }}
        @if ($errors->has('email'))
            <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('dob') ? ' has-error' : '' }}">
    {{ Form::label('dob', 'Date Of Birth', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('dob', null,['class'=>'form-control col-md-3', 'id'=>'dob']) }}
        @if ($errors->has('dob'))
            <span class="help-block">
                <strong>{{ $errors->first('dob') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('joining_date') ? ' has-error' : '' }}">
    {{ Form::label('joining_date', 'Joining Date', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('joining_date', null,['class'=>'form-control col-md-3', 'id'=>'date']) }}
        @if ($errors->has('joining_date'))
            <span class="help-block">
                <strong>{{ $errors->first('joining_date') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('designation_id') ? ' has-error' : '' }}">
    {{ Form::label('designation_id', 'Designation', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('designation_id', $designations, null,['class'=>'form-control', 'placeholder'=>'Select']) }}
        @if ($errors->has('designation_id'))
            <span class="help-block">
                <strong>{{ $errors->first('designation_id') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('workspace_id') ? ' has-error' : '' }}">
    {{ Form::label('workspace_id', 'Workspace', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('workspace_id', $workspaces, null,['class'=>'form-control', 'placeholder'=>'Select','required']) }}
        @if ($errors->has('workspace_id'))
            <span class="help-block">
                <strong>{{ $errors->first('workspace_id') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('employee_type') ? ' has-error' : '' }}">
    {{ Form::label('employee_type', 'Employee Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('employee_type', array_flip(Config::get('common.employee_type')), null,['class'=>'form-control', 'placeholder'=>'Select','required']) }}
        @if ($errors->has('employee_type'))
            <span class="help-block">
                <strong>{{ $errors->first('employee_type') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('present_address') ? ' has-error' : '' }}">
    {{ Form::label('present_address', 'Present Address', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::textarea('present_address', null,['class'=>'form-control col-md-3', 'rows'=>3]) }}
        @if ($errors->has('present_address'))
            <span class="help-block">
                <strong>{{ $errors->first('present_address') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('permanent_address') ? ' has-error' : '' }}">
    {{ Form::label('permanent_address', 'Permanent Address', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::textarea('permanent_address', null,['class'=>'form-control col-md-3', 'rows'=>3]) }}
        @if ($errors->has('permanent_address'))
            <span class="help-block">
                <strong>{{ $errors->first('permanent_address') }}</strong>
            </span>
        @endif
    </div>
</div>

@if(!isset($employee))
<div class="form-group{{ $errors->has('balance') ? ' has-error' : '' }}">
    {{ Form::label('balance', 'Balance', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('balance', null,['class'=>'form-control col-md-3 quantity']) }}
        @if ($errors->has('balance'))
            <span class="help-block">
                <strong>{{ $errors->first('balance') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('due') ? ' has-error' : '' }}">
    {{ Form::label('due', 'Due', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('due', null,['class'=>'form-control col-md-3 quantity']) }}
        @if ($errors->has('due'))
            <span class="help-block">
                <strong>{{ $errors->first('due') }}</strong>
            </span>
        @endif
    </div>
</div>
@endif

<div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
    {{ Form::label('status', 'Status', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('status', Config::get('common.status'), 1,['class'=>'form-control', 'placeholder'=>'Select']) }}
        @if ($errors->has('status'))
            <span class="help-block">
                <strong>{{ $errors->first('status') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-actions">
    <div class="row">
        <div class="text-center col-md-12">
            {{ Form::submit($submitText, ['class'=>'btn btn-circle green']) }}
        </div>
    </div>
</div>

<script type="text/javascript">

    $(function() {
        $( "#date" ).datepicker();
        $( "#dob" ).datepicker();
    });

    jQuery(document).ready(function()
    {
        $(document).on("keyup", ".quantity", function()
        {
            this.value = this.value.replace(/[^0-9\.]/g,'');
        });
    });
</script>