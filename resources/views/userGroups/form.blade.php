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
    {{ Form::label('ordering', 'Order', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('ordering') ? ' has-error' : '' }}">
        {{ Form::text('ordering', null,['class'=>'form-control']) }}
        @if ($errors->has('ordering'))
            <span class="help-block">
                <strong>{{ $errors->first('ordering') }}</strong>
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