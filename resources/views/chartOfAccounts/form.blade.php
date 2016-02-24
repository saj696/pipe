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

<div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">
    {{ Form::label('code', 'Code', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('code', null,['class'=>'form-control']) }}
        @if ($errors->has('code'))
            <span class="help-block">
                <strong>{{ $errors->first('code') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('parent', 'Parent', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('parent', $parents, null,['class'=>'form-control', 'placeholder'=>'Select']) }}
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

<div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
    {{ Form::label('contra_status', 'Contra Status', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-1">
        {{ Form::checkbox('contra_status', 1, null,['class'=>'form-control contra_status']) }}
        @if ($errors->has('contra_status'))
            <span class="help-block">
                <strong>{{ $errors->first('contra_status') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group contra_id_div" style="display: none;">
    {{ Form::label('contra_id', 'Contra', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('contra_id', $contras, null,['class'=>'form-control contra_id', 'placeholder'=>'Select']) }}
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
    $(document).ready(function ()
    {
        $(document).on('click', '.contra_status', function ()
        {
            if($(this).prop('checked'))
            {
                $('.contra_id_div').show();
            }
            else
            {
                $('.contra_id_div').hide();
                $('.contra_id').val('');
            }
        });
    })
</script>