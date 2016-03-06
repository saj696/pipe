{!! csrf_field() !!}

<div class="form-group">
    {{ Form::label('name', 'Name', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('name') ? ' has-error' : '' }}">
        {{ Form::text('name', null,['class'=>'form-control']) }}
        @if ($errors->has('name'))
            <span class="help-block">
                <strong>{{ $errors->first('name') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('salary', 'Salary', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('salary') ? ' has-error' : '' }}">
        {{ Form::text('salary', null,['class'=>'form-control quantity']) }}
        @if ($errors->has('salary'))
            <span class="help-block">
                <strong>{{ $errors->first('salary') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('hourly_rate', 'Hourly Rate', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('hourly_rate') ? ' has-error' : '' }}">
        {{ Form::text('hourly_rate', null,['class'=>'form-control quantity']) }}
        @if ($errors->has('hourly_rate'))
            <span class="help-block">
                <strong>{{ $errors->first('hourly_rate') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('status', 'Status', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('status') ? ' has-error' : '' }}">
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

    jQuery(document).ready(function () {
        $(document).on("keyup", ".quantity", function () {
            this.value = this.value.replace(/[^0-9\.]/g, '');
        });
    });
</script>