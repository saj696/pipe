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
    {{ Form::label('mobile', 'Mobile', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('mobile') ? ' has-error' : '' }}">
        {{ Form::text('mobile',null,['class'=>'form-control']) }}
        @if ($errors->has('mobile'))
            <span class="help-block">
                <strong>{{ $errors->first('mobile') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('address', 'Address', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('address') ? ' has-error' : '' }}">
        {{ Form::textarea('address',null,['class'=>'form-control','rows'=>'2']) }}
        @if ($errors->has('address'))
            <span class="help-block">
                <strong>{{ $errors->first('address') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('type', 'Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('type') ? ' has-error' : '' }}">
        {{ Form::select('type', Config::get('common.customer_type'), null,['class'=>'form-control', 'id'=>'customer_type', 'placeholder'=>'Select']) }}
        @if ($errors->has('type'))
            <span class="help-block">
                <strong>{{ $errors->first('type') }}</strong>
            </span>
        @endif
    </div>
</div>

@if(!isset($customer))
<div class="form-group">
    {{ Form::label('balance', 'Balance', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('balance') ? ' has-error' : '' }}">
        {{ Form::number('balance',null,['class'=>'form-control','min'=>0, 'step'=>0.01]) }}
        @if ($errors->has('balance'))
            <span class="help-block">
                <strong>{{ $errors->first('balance') }}</strong>
            </span>
        @endif
    </div>
</div>
@endif

@if(!isset($customer))
<div class="form-group">
    {{ Form::label('due', 'Due', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('due') ? ' has-error' : '' }}">
        {{ Form::number('due',null,['class'=>'form-control','min'=>0, 'step'=>0.01]) }}
        @if ($errors->has('due'))
            <span class="help-block">
                <strong>{{ $errors->first('due') }}</strong>
            </span>
        @endif
    </div>
</div>
@endif

<div id="business_info" style="display: none">
    <div class="form-group">
        {{ Form::label('business_name', 'Business Name', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7{{ $errors->has('business_name') ? ' has-error' : '' }}">
            {{ Form::text('business_name',null,['class'=>'form-control']) }}
            @if ($errors->has('business_name'))
                <span class="help-block">
                <strong>{{ $errors->first('business_name') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('business_address', 'Business Address', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7{{ $errors->has('business_address') ? ' has-error' : '' }}">
            {{ Form::textarea('business_address',null,['class'=>'form-control','rows'=>'2']) }}
            @if ($errors->has('business_address'))
                <span class="help-block">
                <strong>{{ $errors->first('business_address') }}</strong>
            </span>
            @endif
        </div>
    </div>
</div>


<div class="form-group">
    {{ Form::label('picture', 'Picture', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-3{{ $errors->has('picture') ? ' has-error' : '' }}">
        {{ Form::file('picture', null,['class'=>'form-control']) }}
        @if ($errors->has('picture'))
            <span class="help-block">
                <strong>{{ $errors->first('picture') }}</strong>
            </span>
        @endif
    </div>

    <div>
        @if(isset($customer->picture))
            <img width="100" height="120" src="{{URL::to('/public')}}/image/customer/{{ $customer->picture }}"
                 alt="picture">
        @endif
    </div>
</div>

@if(isset($customer->status))
    <div class="form-group">
        {{ Form::label('status', 'Status', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7{{ $errors->has('status') ? ' has-error' : '' }}">
            {{ Form::select('status', Config::get('common.status'), null,['class'=>'form-control', 'placeholder'=>'Select']) }}
            @if ($errors->has('status'))
                <span class="help-block">
                <strong>{{ $errors->first('status') }}</strong>
            </span>
            @endif
        </div>
    </div>
@endif


<div class="form-actions">
    <div class="row">
        <div class="text-center col-md-12">
            {{ Form::submit($submitText, ['class'=>'btn btn-circle green']) }}
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $(document).on('change', '#customer_type', function () {
            var type = parseInt($(this).val());
            console.log(type)
            if (type == 1) {
                $('#business_info').show();
            } else {
                $('#business_info').hide();
            }

        });

        var type = $('#customer_type').val();
        if (type == 1) {
            $('#business_info').show();
        }
    })
</script>