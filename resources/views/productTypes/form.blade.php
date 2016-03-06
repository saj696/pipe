{!! csrf_field() !!}
<div class="form-group">
    {{ Form::label('title', 'Title', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('title') ? ' has-error' : '' }}">
        {{ Form::text('title', null,['class'=>'form-control']) }}
        @if ($errors->has('title'))
            <span class="help-block">
                <strong>{{ $errors->first('title') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group">
    {{ Form::label('status', 'Status', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('status') ? ' has-error' : '' }}">
        {{ Form::select('status',Config::get('common.status'),null,['class'=>'form-control']) }}
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
    $(document).ready(function () {

    });

</script>