{!! csrf_field() !!}

<div class="form-group">
    {{ Form::label('opening_stock', 'Opening Stock', ['class'=>'col-md-4 control-label']) }}
    <div class="col-md-4{{ $errors->has('opening_stock') ? ' has-error' : '' }}">
        {{ Form::number('opening_stock', null,['class'=>'form-control', 'min'=>1]) }}
        @if ($errors->has('opening_stock'))
            <span class="help-block">
                <strong>{{ $errors->first('opening_stock') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-actions">
    <div class="row">
        <div class="text-center col-md-12">
            {{ Form::submit($submitText, ['class'=>'btn btn-circle red']) }}
        </div>
    </div>
</div>