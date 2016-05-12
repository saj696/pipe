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

@if(!isset($provider))
<div class="form-group">
    {{ Form::label('balance', 'Balance', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('balance') ? ' has-error' : '' }}">
        {{ Form::text('balance',null,['class'=>'form-control']) }}
        @if ($errors->has('balance'))
            <span class="help-block">
                <strong>{{ $errors->first('balance') }}</strong>
            </span>
        @endif
    </div>
</div>
@endif

@if(!isset($provider))
<div class="form-group">
    {{ Form::label('due', 'Due', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('due') ? ' has-error' : '' }}">
        {{ Form::text('due',null,['class'=>'form-control']) }}
        @if ($errors->has('due'))
            <span class="help-block">
                <strong>{{ $errors->first('due') }}</strong>
            </span>
        @endif
    </div>
</div>
@endif

<div class="form-group">
    {{ Form::label('company_name', 'Company Name', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('company_name') ? ' has-error' : '' }}">
        {{ Form::text('company_name',null,['class'=>'form-control']) }}
        @if ($errors->has('company_name'))
            <span class="help-block">
                <strong>{{ $errors->first('company_name') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('company_address', 'Company Address', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('company_address') ? ' has-error' : '' }}">
        {{ Form::textarea('company_address',null,['class'=>'form-control','rows'=>'2']) }}
        @if ($errors->has('company_address'))
            <span class="help-block">
                <strong>{{ $errors->first('company_address') }}</strong>
            </span>
        @endif
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
        @if(isset($provider->picture) && strlen($provider->picture)>0)
            <img width="100" height="120" src="{{URL::to('/public')}}/image/provider/{{ $provider->picture }}" alt="picture">
        @else
            <img width="80" height="80" src="{{URL::to('/public')}}/image/provider/no_image.jpg" alt="picture">
        @endif
    </div>
</div>

@if(isset($provider->status))
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