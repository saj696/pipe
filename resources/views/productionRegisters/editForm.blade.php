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
    {{ Form::label('product_id', 'Product', ['class'=>'col-md-4 control-label']) }}
    <div class="col-md-4{{ $errors->has('product_id') ? ' has-error' : '' }}">
        {{ Form::select('product_id', $products, null,['class'=>'form-control', 'placeholder'=>'Select', 'disabled'=>'disabled']) }}
        @if ($errors->has('product_id'))
            <span class="help-block">
                <strong>{{ $errors->first('product_id') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('production', 'production', ['class'=>'col-md-4 control-label']) }}
    <div class="col-md-4{{ $errors->has('production') ? ' has-error' : '' }}">
        {{ Form::text('production', null,['class'=>'form-control col-md-2']) }}
        @if ($errors->has('production'))
            <span class="help-block">
                <strong>{{ $errors->first('production') }}</strong>
            </span>
        @endif
    </div>
</div>

{{--<div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">--}}
{{--{{ Form::label('status', 'Status', ['class'=>'col-md-4 control-label']) }}--}}
{{--<div class="col-md-4">--}}
{{--{{ Form::select('status', Config::get('common.status'), null,['class'=>'form-control', 'placeholder'=>'Select']) }}--}}
{{--@if ($errors->has('status'))--}}
{{--<span class="help-block">--}}
{{--<strong>{{ $errors->first('status') }}</strong>--}}
{{--</span>--}}
{{--@endif--}}
{{--</div>--}}
{{--</div>--}}

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