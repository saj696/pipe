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

<div class="form-group{{ $errors->has('parent') ? ' has-error' : '' }}">
    {{ Form::label('parent', 'Parent', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('parent', $parents, null,['class'=>'form-control', 'id'=>'parent', 'placeholder'=>'Select']) }}
        @if ($errors->has('parent'))
            <span class="help-block">
                <strong>{{ $errors->first('parent') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('location') ? ' has-error' : '' }}">
    {{ Form::label('location', 'Location', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('location', null,['class'=>'form-control']) }}
        @if ($errors->has('location'))
            <span class="help-block">
                <strong>{{ $errors->first('location') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
    {{ Form::label('status', 'Status', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
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
        <div class="col-md-offset-3 col-md-9">
        {{ Form::submit($submitText, ['class'=>'btn green']) }}
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function ()
    {
        $(document).on("change","#parent",function()
        {
            var parent_id = $(this).val();
            var type_id = $('#type').val();

            if(parent_id>0)
            {
                $.ajax({
                    url: '{{ route('ajax.parent_select') }}',
                    type: 'POST',
                    dataType: "JSON",
                    data:{parent_id:parent_id, type_id:type_id},
                    success: function (data, status)
                    {
                        if(type_id==2 && data==1)
                        {
                            $("#parent").val('');
                            alert('You can not add a delivery center under a showroom!');
                        }
                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");
                    }
                });
            }
        });
    });
</script>