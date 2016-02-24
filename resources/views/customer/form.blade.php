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

<div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
    {{ Form::label('mobile', 'Mobile', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('mobile',null,['class'=>'form-control']) }}
        @if ($errors->has('mobile'))
            <span class="help-block">
                <strong>{{ $errors->first('mobile') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
    {{ Form::label('address', 'Address', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::textarea('address',null,['class'=>'form-control','rows'=>'2']) }}
        @if ($errors->has('address'))
            <span class="help-block">
                <strong>{{ $errors->first('address') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
    {{ Form::label('type', 'Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('type', Config::get('common.customer_type'), null,['class'=>'form-control', 'id'=>'customer_type', 'placeholder'=>'Select']) }}
        @if ($errors->has('type'))
            <span class="help-block">
                <strong>{{ $errors->first('type') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('balance') ? ' has-error' : '' }}">
    {{ Form::label('balance', 'Balance', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('balance',null,['class'=>'form-control']) }}
        @if ($errors->has('balance'))
            <span class="help-block">
                <strong>{{ $errors->first('balance') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('due') ? ' has-error' : '' }}">
    {{ Form::label('due', 'Due', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('due',null,['class'=>'form-control']) }}
        @if ($errors->has('due'))
            <span class="help-block">
                <strong>{{ $errors->first('due') }}</strong>
            </span>
        @endif
    </div>
</div>

<div id="business_info" style="display: none">
    <div class="form-group{{ $errors->has('business_name') ? ' has-error' : '' }}">
        {{ Form::label('business_name', 'Business Name', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7">
            {{ Form::text('business_name',null,['class'=>'form-control']) }}
            @if ($errors->has('business_name'))
                <span class="help-block">
                <strong>{{ $errors->first('business_name') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('business_address') ? ' has-error' : '' }}">
        {{ Form::label('business_address', 'Business Address', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7">
            {{ Form::textarea('business_address',null,['class'=>'form-control','rows'=>'2']) }}
            @if ($errors->has('business_address'))
                <span class="help-block">
                <strong>{{ $errors->first('business_address') }}</strong>
            </span>
            @endif
        </div>
    </div>
</div>


<div class="form-group{{ $errors->has('picture') ? ' has-error' : '' }}">
    {{ Form::label('picture', 'Picture', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-3">
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
@endif


<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
            {{ Form::submit($submitText, ['class'=>'btn green']) }}
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