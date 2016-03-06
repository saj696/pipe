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
    {{ Form::label('product_type_id', 'Product Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('product_type_id') ? ' has-error' : '' }}">
        {{ Form::select('product_type_id',$product_types, null,['class'=>'form-control','placeholder'=>'Select']) }}
        @if ($errors->has('product_type_id'))
            <span class="help-block">
                <strong>{{ $errors->first('product_type_id') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('diameter', 'Diameter', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('diameter') ? ' has-error' : '' }}">
        {{ Form::number('diameter', null,['class'=>'form-control']) }}
        @if ($errors->has('diameter'))
            <span class="help-block">
                <strong>{{ $errors->first('diameter') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group}">
    {{ Form::label('weight', 'Weight', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('weight') ? ' has-error' : '' }">
        {{ Form::text('weight', null,['class'=>'form-control']) }}
        @if ($errors->has('weight'))
            <span class="help-block">
                <strong>{{ $errors->first('weight') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group">
    {{ Form::label('length', 'Length', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('length') ? ' has-error' : '' }}">
        {{ Form::number('length', null,['class'=>'form-control']) }}
        @if ($errors->has('length'))
            <span class="help-block">
                <strong>{{ $errors->first('length') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group">
    {{ Form::label('color', 'Color', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('color') ? ' has-error' : '' }}">
        {{ Form::select('color',$color ,null,['class'=>'form-control']) }}
        @if ($errors->has('length'))
            <span class="help-block">
                <strong>{{ $errors->first('color') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group">
    {{ Form::label('wholesale_price', 'Wholesale Price', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('wholesale_price') ? ' has-error' : '' }}">
        {{ Form::text('wholesale_price',null,['class'=>'form-control']) }}
        @if ($errors->has('length'))
            <span class="help-block">
                <strong>{{ $errors->first('wholesale_price') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group">
    {{ Form::label('retail_price', 'Retail Price', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('retail_price') ? ' has-error' : '' }}">
        {{ Form::text('retail_price',null,['class'=>'form-control']) }}
        @if ($errors->has('length'))
            <span class="help-block">
                <strong>{{ $errors->first('retail_price') }}</strong>
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