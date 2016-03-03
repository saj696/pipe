{!! csrf_field() !!}

<div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
    {{ Form::label('date', 'Date', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('date', null,['class'=>'form-control transaction_date']) }}
        @if ($errors->has('date'))
            <span class="help-block">
                <strong>{{ $errors->first('date') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('workspace_to') ? ' has-error' : '' }}">
    {{ Form::label('workspace_to', 'Workspace', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('workspace_to', $workspaces, null,['class'=>'form-control', 'id'=>'workspace_to', 'placeholder'=>'Select']) }}
        @if ($errors->has('workspace_to'))
            <span class="help-block">
                <strong>{{ $errors->first('workspace_to') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
    {{ Form::label('amount', 'Amount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('amount', null,['class'=>'form-control amount']) }}
        {{ Form::hidden('total_amount', $totalCash,['class'=>'form-control total_amount']) }}
        @if ($errors->has('amount'))
            <span class="help-block">
                <strong>{{ $errors->first('amount') }}</strong>
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
        $( ".transaction_date" ).datepicker({ maxDate: new Date });
    });

    $(document).ready(function ()
    {
        $(document).on('keyup', '.amount', function ()
        {
            var total_amount = parseFloat($('.total_amount').val());
            var amount = parseFloat($(this).val());

            if(amount>total_amount)
            {
                alert('Exceeds Total Cash!');
                $(this).val(0);
            }
        });
    });
</script>