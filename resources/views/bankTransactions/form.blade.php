{!! csrf_field() !!}
<div class="form-group">
    {{ Form::label('bank_id', 'Bank Name', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('bank_id') ? ' has-error' : '' }}">
        {{ Form::select('bank_id', $banks, null,['class'=>'form-control select2me','placeholder'=>'Select']) }}
        @if ($errors->has('bank_id'))
            <span class="help-block">
                <strong>{{ $errors->first('bank_id') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('transaction_type', 'Transaction Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('transaction_type') ? ' has-error' : '' }}">
        {{ Form::select('transaction_type',Config::get('common.bank_transaction_type'),null,['class'=>'form-control select2me','placeholder'=>'Select']) }}
        @if ($errors->has('transaction_type'))
            <span class="help-block">
                <strong>{{ $errors->first('transaction_type') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('amount', 'Amount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('amount') ? ' has-error' : '' }}">
        {{ Form::text('amount',null,['class'=>'form-control']) }}
        @if ($errors->has('amount'))
            <span class="help-block">
                <strong>{{ $errors->first('amount') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('transaction_date', 'Transaction Date', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('transaction_date') ? ' has-error' : '' }}">
        {{ Form::text('transaction_date',null,['class'=>'form-control transaction_date']) }}
        @if ($errors->has('transaction_date'))
            <span class="help-block">
                <strong>{{ $errors->first('transaction_date') }}</strong>
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
    $(function() {
        $( ".transaction_date" ).datepicker();
    });
</script>