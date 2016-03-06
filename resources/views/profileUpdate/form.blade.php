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
    {{ Form::label('present_address', 'Present Address', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('present_address') ? ' has-error' : '' }}">
        {{ Form::textarea('present_address', null, ['class'=>'form-control', 'rows'=>2]) }}
        @if ($errors->has('present_address'))
            <span class="help-block">
                <strong>{{ $errors->first('present_address') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('permanent_address', 'Permanent Address', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('permanent_address') ? ' has-error' : '' }}">
        {{ Form::textarea('permanent_address', null, ['class'=>'form-control', 'rows'=>2]) }}
        @if ($errors->has('permanent_address'))
            <span class="help-block">
                <strong>{{ $errors->first('permanent_address') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('photo', 'Photo', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-3{{ $errors->has('photo') ? ' has-error' : '' }}">
        {{ Form::file('photo', null,['class'=>'form-control']) }}
        @if ($errors->has('photo'))
            <span class="help-block">
                <strong>{{ $errors->first('photo') }}</strong>
            </span>
        @endif
    </div>
    <div class="col-md-2">
        @if(isset($user->photo))
            <img width="80" height="80" src="{{URL::to('/public')}}/image/user/{{ $user->photo }}" alt="picture">
        @else
            <img width="80" height="80" src="{{URL::to('/public')}}/image/user/no_image.jpg" alt="picture">
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