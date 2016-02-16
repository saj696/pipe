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

<div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
    {{ Form::label('type', 'Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('type', $types, null,['class'=>'form-control', 'id'=>'type', 'placeholder'=>'Select']) }}
        @if ($errors->has('type'))
            <span class="help-block">
                <strong>{{ $errors->first('type') }}</strong>
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
        <div class="text-center col-md-12">
        {{ Form::submit($submitText, ['class'=>'btn green']) }}
        </div>
    </div>
</div>