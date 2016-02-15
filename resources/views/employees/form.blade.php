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
        {{ Form::text('dob', null,['class'=>'form-control col-md-3']) }}
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
        {{ Form::text('joining_date', null,['class'=>'form-control col-md-3']) }}
        @if ($errors->has('joining_date'))
            <span class="help-block">
                <strong>{{ $errors->first('joining_date') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('designation') ? ' has-error' : '' }}">
    {{ Form::label('designation', 'Designation', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('designation', $designations, null,['class'=>'form-control', 'placeholder'=>'Select']) }}
        @if ($errors->has('designation'))
            <span class="help-block">
                <strong>{{ $errors->first('designation') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('present_address') ? ' has-error' : '' }}">
    {{ Form::label('present_address', 'Present Address', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('present_address', null,['class'=>'form-control col-md-3']) }}
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
        {{ Form::text('permanent_address', null,['class'=>'form-control col-md-3']) }}
        @if ($errors->has('permanent_address'))
            <span class="help-block">
                <strong>{{ $errors->first('permanent_address') }}</strong>
            </span>
        @endif
    </div>
</div>

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
        <div class="col-md-offset-3 col-md-9">
        {{ Form::submit($submitText, ['class'=>'btn green']) }}
        </div>
    </div>
</div>

<script type="text/javascript">

    jQuery(document).ready(function()
    {
        $(document).on("keyup", ".quantity", function()
        {
            this.value = this.value.replace(/[^0-9\.]/g,'');
        });
    });
</script>