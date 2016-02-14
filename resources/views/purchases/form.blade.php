{!! csrf_field() !!}
<div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
    {{ Form::label('title', 'Title', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('title', null,['class'=>'form-control']) }}
        @if ($errors->has('title'))
            <span class="help-block">
                <strong>{{ $errors->first('title') }}</strong>
            </span>
        @endif
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function ()
    {

    });

</script>