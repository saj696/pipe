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

<div class="form-group">
    {{ Form::label('component_id', 'Component', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('component_id', $components, null,['class'=>'form-control']) }}
    </div>
</div>

<div class="form-group{{ $errors->has('icon') ? ' has-error' : '' }}">
    {{ Form::label('icon', 'Icon', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('icon', null,['class'=>'form-control']) }}
        @if ($errors->has('icon'))
            <span class="help-block">
                <strong>{{ $errors->first('icon') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
    {{ Form::label('description', 'Description', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::textarea('description', null,['class'=>'form-control', 'rows'=>'3']) }}
        @if ($errors->has('body'))
            <span class="help-block">
                <strong>{{ $errors->first('description') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('ordering') ? ' has-error' : '' }}">
    {{ Form::label('ordering', 'Order', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('ordering', null,['class'=>'form-control']) }}
        @if ($errors->has('ordering'))
            <span class="help-block">
                <strong>{{ $errors->first('ordering') }}</strong>
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