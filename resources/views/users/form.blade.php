{!! csrf_field() !!}

<div class="form-group{{ $errors->has('name_en') ? ' has-error' : '' }}">
    {{ Form::label('name_en', 'Name EN', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('name_en', null,['class'=>'form-control']) }}
        @if ($errors->has('name_en'))
            <span class="help-block">
                <strong>{{ $errors->first('name_en') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('name_bn') ? ' has-error' : '' }}">
    {{ Form::label('name_bn', 'Name BN', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('name_bn', null,['class'=>'form-control']) }}
        @if ($errors->has('name_bn'))
            <span class="help-block">
                <strong>{{ $errors->first('name_bn') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
    {{ Form::label('username', 'Username', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('username', null,['class'=>'form-control']) }}
        @if ($errors->has('username'))
            <span class="help-block">
                <strong>{{ $errors->first('username') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
    {{ Form::label('password', 'Password', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::password('password', ['class'=>'form-control']) }}
        @if ($errors->has('password'))
            <span class="help-block">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
    {{ Form::label('email', 'Email', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('email', null, ['class'=>'form-control']) }}
        @if ($errors->has('email'))
            <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('user_group_id', 'User Group', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('user_group_id', $groups, null,['class'=>'form-control', 'placeholder'=>'Select']) }}
    </div>
</div>

<div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
    {{ Form::label('status', 'Status', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('status', ['1'=>'Active','0'=>'Inactive'], null,['class'=>'form-control', 'placeholder'=>'Select']) }}
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