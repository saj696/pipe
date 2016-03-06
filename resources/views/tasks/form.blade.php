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
    {{ Form::label('component_id', 'Component', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('component_id') ? ' has-error' : '' }}">
        {{ Form::select('component_id', $components, null,['class'=>'form-control component_id','placeholder'=>'Select']) }}
        @if ($errors->has('component_id'))
            <span class="help-block">
                <strong>{{ $errors->first('component_id') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group module_div"
     style="display: {{ isset($task)?'show':'none' }};">
    {{ Form::label('module_id', 'Module', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('module_id') ? ' has-error' : '' }}">
        {{ Form::select('module_id', $modules, null,['class'=>'form-control module_id','placeholder'=>'Select']) }}
        @if ($errors->has('module_id'))
            <span class="help-block">
                <strong>{{ $errors->first('module_id') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('route', 'Route', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('route') ? ' has-error' : '' }}">
        {{ Form::text('route', null,['class'=>'form-control']) }}
        @if ($errors->has('route'))
            <span class="help-block">
                <strong>{{ $errors->first('route') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('icon', 'Icon', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('icon') ? ' has-error' : '' }}">
        {{ Form::text('icon', null,['class'=>'form-control']) }}
        @if ($errors->has('icon'))
            <span class="help-block">
                <strong>{{ $errors->first('icon') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('description', 'Description', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('description') ? ' has-error' : '' }}">
        {{ Form::textarea('description', null,['class'=>'form-control', 'rows'=>'3']) }}
        @if ($errors->has('body'))
            <span class="help-block">
                <strong>{{ $errors->first('description') }}</strong>
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

<div class="form-actions">
    <div class="row">
        <div class="text-center col-md-12">
            {{ Form::submit($submitText, ['class'=>'btn btn-circle green']) }}
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $(document).on("change", "#component_id", function () {
            $(".module_div").show();
            $(".module_id").val("");
            var component_id = $(this).val();

            if (component_id > 0) {
                $.ajax({
                    url: '{{ route('ajax.module_select') }}',
                    type: 'POST',
                    dataType: "JSON",
                    data: {component_id: component_id},
                    success: function (data, status) {
                        $('#module_id').html('<option value="">' + 'Select' + '</option>');
                        $.each(data, function (key, element) {
                            $('#module_id').append("<option value='" + key + "'>" + element + "</option>");
                        });
                    },
                    error: function (xhr, desc, err) {
                        console.log("error");
                    }
                });
            }
            else {
                $(".module_div").hide();
            }
        });
    });

</script>