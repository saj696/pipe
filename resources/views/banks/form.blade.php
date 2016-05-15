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
    {{ Form::label('account_name', 'Account Name', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('account_name') ? ' has-error' : '' }}">
        {{ Form::text('account_name',null,['class'=>'form-control']) }}
        @if ($errors->has('account_name'))
            <span class="help-block">
                <strong>{{ $errors->first('account_name') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('account_no', 'Acount No.', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('account_no') ? ' has-error' : '' }}">
        {{ Form::text('account_no',null,['class'=>'form-control']) }}
        @if ($errors->has('account_no'))
            <span class="help-block">
                <strong>{{ $errors->first('account_no') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('account_director', 'Account Director', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('account_director') ? ' has-error' : '' }}">
        {{ Form::text('account_director',null,['class'=>'form-control']) }}
        @if ($errors->has('account_director'))
            <span class="help-block">
                <strong>{{ $errors->first('account_director') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('start_date', 'Start Date', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('start_date') ? ' has-error' : '' }}">
        {{ Form::text('start_date',null,['class'=>'form-control start_date']) }}
        @if ($errors->has('start_date'))
            <span class="help-block">
                <strong>{{ $errors->first('start_date') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('account_type', 'Acount Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('account_type') ? ' has-error' : '' }}">
        {{ Form::select('account_type', Config::get('common.account_type'),null,['class'=>'form-control']) }}
        @if ($errors->has('account_type'))
            <span class="help-block">
                <strong>{{ $errors->first('account_type') }}</strong>
            </span>
        @endif
    </div>
</div>

@if(!isset($bank))
<div class="form-group">
    {{ Form::label('account_code', 'Chart of Account Code', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('account_code') ? ' has-error' : '' }}">
        {{ Form::number('account_code',null,['class'=>'form-control', 'required']) }}
        @if ($errors->has('account_code'))
            <span class="help-block">
                <strong>{{ $errors->first('account_code') }}</strong>
            </span>
        @endif
    </div>
</div>
@endif

@if(!isset($bank))
<div class="form-group">
    {{ Form::label('opening_balance', 'Opening Balance', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('opening_balance') ? ' has-error' : '' }}">
        {{ Form::number('opening_balance',null,['class'=>'form-control']) }}
        @if ($errors->has('opening_balance'))
            <span class="help-block">
                <strong>{{ $errors->first('opening_balance') }}</strong>
            </span>
        @endif
    </div>
</div>
@endif

@if(isset($bank->status))
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

<script type="text/javascript">
    $(function() {
        $( ".start_date" ).datepicker();
    });
</script>