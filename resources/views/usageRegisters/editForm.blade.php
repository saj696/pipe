{!! csrf_field() !!}

<div class="form-group">
    {{ Form::label('date', 'Date', ['class'=>'col-md-4 control-label']) }}
    <div class="col-md-4{{ $errors->has('date') ? ' has-error' : '' }}">
        {{ Form::text('date', null,['class'=>'form-control col-md-2']) }}
        @if ($errors->has('name'))
            <span class="help-block">
                <strong>{{ $errors->first('date') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('material_id', 'Material', ['class'=>'col-md-4 control-label']) }}
    <div class="col-md-4{{ $errors->has('material_id') ? ' has-error' : '' }}">
        {{ Form::select('material_id', $materials, null,['class'=>'form-control', 'placeholder'=>'Select']) }}
        @if ($errors->has('material_id'))
            <span class="help-block">
                <strong>{{ $errors->first('material_id') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('usage', 'usage', ['class'=>'col-md-4 control-label']) }}
    <div class="col-md-4{{ $errors->has('usage') ? ' has-error' : '' }}">
        {{ Form::text('usage', null,['class'=>'form-control col-md-2']) }}
        @if ($errors->has('usage'))
            <span class="help-block">
                <strong>{{ $errors->first('usage') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('status', 'Status', ['class'=>'col-md-4 control-label']) }}
    <div class="col-md-4{{ $errors->has('status') ? ' has-error' : '' }}">
        {{ Form::select('status', Config::get('common.status'), null,['class'=>'form-control', 'placeholder'=>'Select']) }}
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
    $(function () {
        $("#date").datepicker({maxDate: new Date});
    });
</script>