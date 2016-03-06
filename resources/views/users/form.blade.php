{!! csrf_field() !!}

<div class="form-group">
    {{ Form::label('name_en', 'Name EN', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('name_en') ? ' has-error' : '' }}">
        {{ Form::text('name_en', null,['class'=>'form-control']) }}
        @if ($errors->has('name_en'))
            <span class="help-block">
                <strong>{{ $errors->first('name_en') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('name_bn', 'Name BN', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('name_bn') ? ' has-error' : '' }}">
        {{ Form::text('name_bn', null,['class'=>'form-control']) }}
        @if ($errors->has('name_bn'))
            <span class="help-block">
                <strong>{{ $errors->first('name_bn') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('username', 'Username', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('username') ? ' has-error' : '' }}">
        {{ Form::text('username', null,['class'=>'form-control']) }}
        @if ($errors->has('username'))
            <span class="help-block">
                <strong>{{ $errors->first('username') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('password', 'Password', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('password') ? ' has-error' : '' }}">
        {{ Form::password('password', ['class'=>'form-control']) }}
        @if ($errors->has('password'))
            <span class="help-block">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('email', 'Email', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('email') ? ' has-error' : '' }}">
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
    <div class="col-md-7{{ $errors->has('user_group_id') ? ' has-error' : '' }}">
        {{ Form::select('user_group_id', $groups, null,['class'=>'form-control', 'placeholder'=>'Select']) }}
        @if ($errors->has('user_group_id'))
            <span class="help-block">
                <strong>{{ $errors->first('user_group_id') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group">
    {{ Form::label('workspace_id', 'Workspace', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('workspace_id') ? ' has-error' : '' }}">
        {{ Form::select('workspace_id', $workspaces, null,['class'=>'form-control', 'placeholder'=>'Select']) }}
        @if ($errors->has('workspace_id'))
            <span class="help-block">
                <strong>{{ $errors->first('workspace_id') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('status', 'Status', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('status') ? ' has-error' : '' }}">
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