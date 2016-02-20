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

<div class="form-group{{ $errors->has('account_code') ? ' has-error' : '' }}">
    {{ Form::label('account_code', 'Account', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('account_code', $accounts, null,['class'=>'form-control', 'id'=>'account_type', 'placeholder'=>'Select']) }}
        @if ($errors->has('account_code'))
            <span class="help-block">
                <strong>{{ $errors->first('account_code') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="to_whom_type_div form-group{{ $errors->has('to_whom_type') ? ' has-error' : '' }}" style="display: {{ (isset($recorder->to_whom_type) && $recorder->to_whom_type>0)?'show':'none' }};">
    {{ Form::label('to_whom_type', 'To Whom Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('to_whom_type', $types, null,['class'=>'form-control', 'id'=>'to_whom_type', 'placeholder'=>'Select']) }}
        @if ($errors->has('to_whom_type'))
            <span class="help-block">
                <strong>{{ $errors->first('to_whom_type') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="to_whom_div" style="display: {{ (isset($recorder->to_whom_type) && $recorder->to_whom_type>0)?'show':'none' }};">
    @if(isset($recorder->to_whom_type) && $recorder->to_whom_type>0)
        @if($recorder->to_whom_type==1)
            <?php
            $to_whom_data = $customers;
            $label = 'Customer';
            ?>
        @elseif($recorder->to_whom_type==2)
            <?php
            $to_whom_data = $suppliers;
            $label = 'Supplier';
            ?>
        @elseif($recorder->to_whom_type==3)
            <?php
            $to_whom_data = $employees;
            $label = 'Employee';
            ?>
        @endif

        <div class="form-group">
            {{ Form::label('to_whom', $label, ['class'=>'col-md-3 control-label']) }}
            <div class="col-md-7">
                {{ Form::select('to_whom', $to_whom_data, null,['class'=>'form-control employee_customer_supplier','placeholder'=>'Select']) }}
            </div>
        </div>
    @endif
</div>

<div class="from_whom_type_div form-group{{ $errors->has('from_whom_type') ? ' has-error' : '' }}" style="display: {{ (isset($recorder->from_whom_type) && $recorder->from_whom_type>0)?'show':'none' }};">
    {{ Form::label('from_whom_type', 'From Whom Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('from_whom_type', $types, null,['class'=>'form-control', 'id'=>'from_whom_type', 'placeholder'=>'Select']) }}
        @if ($errors->has('from_whom_type'))
            <span class="help-block">
                <strong>{{ $errors->first('from_whom_type') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="from_whom_div" style="display: {{ (isset($recorder->from_whom_type) && $recorder->from_whom_type>0)?'show':'none' }};">
    @if(isset($recorder->from_whom_type) && $recorder->from_whom_type>0)
        @if($recorder->from_whom_type==1)
            <?php
            $from_whom_data = $customers;
            $label = 'Customer';
            ?>
        @elseif($recorder->from_whom_type==2)
            <?php
            $from_whom_data = $suppliers;
            $label = 'Supplier';
            ?>
        @elseif($recorder->from_whom_type==3)
            <?php
            $from_whom_data = $employees;
            $label = 'Employee';
            ?>
        @endif

        <div class="form-group">
            {{ Form::label('from_whom', $label, ['class'=>'col-md-3 control-label']) }}
            <div class="col-md-7">
                {{ Form::select('from_whom', $from_whom_data, null,['class'=>'form-control employee_customer_supplier','placeholder'=>'Select']) }}
            </div>
        </div>
    @endif
</div>

<div class="total_amount_div form-group{{ $errors->has('total_amount') ? ' has-error' : '' }}" style="display: {{ (isset($recorder->total_amount) && $recorder->total_amount>0)?'show':'none' }};">
    {{ Form::label('total_amount', 'Total Amount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('total_amount', null,['class'=>'form-control total_amount']) }}
        @if ($errors->has('total_amount'))
            <span class="help-block">
                <strong>{{ $errors->first('total_amount') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="amount_div form-group{{ $errors->has('amount') ? ' has-error' : '' }}" style="display: {{ (isset($recorder->amount) && $recorder->amount>0)?'show':'none' }};">
    {{ Form::label('amount', 'Amount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('amount', null,['class'=>'form-control amount']) }}
        @if ($errors->has('amount'))
            <span class="help-block">
                <strong>{{ $errors->first('amount') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group due_div" style="display: {{ (isset($recorder->total_amount) && $recorder->total_amount>0)?'show':'none' }};">
    {{ Form::label('due', 'Due', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('due', (isset($recorder->total_amount) && isset($recorder->amount))?($recorder->total_amount - $recorder->amount):null,['class'=>'form-control due']) }}
    </div>
</div>

<div class="transaction_detail_div form-group{{ $errors->has('transaction_detail') ? ' has-error' : '' }}" style="display: {{ (isset($recorder->transaction_detail) && strlen($recorder->transaction_detail)>0)?'show':'none' }};">
    {{ Form::label('transaction_detail', 'Transaction Detail', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::textarea('transaction_detail', null,['class'=>'form-control', 'rows'=>3]) }}
        @if ($errors->has('transaction_detail'))
            <span class="help-block">
                <strong>{{ $errors->first('transaction_detail') }}</strong>
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

    $(document).ready(function ()
    {
        $(document).on('change', '#account_type', function()
        {
            var code = $(this).val();
            var slice = code.substring(0,1);

            if(slice==1 || slice==2 || slice==3)
            {
                $('.from_whom_type_div').show();
                $('.to_whom_type_div').hide();
                $('#to_whom_type').val('');
                $('.to_whom_div').empty();

                $('.total_amount_div').show();
                $('.amount_div').show();
                $('.due_div').show();
                $('.transaction_detail_div').show();
            }
            else if(slice==4)
            {
                $('.to_whom_type_div').show();
                $('.from_whom_type_div').hide();
                $('#from_whom_type').val('');
                $('.from_whom_div').empty();

                $('.total_amount_div').show();
                $('.amount_div').show();
                $('.due_div').show();
                $('.transaction_detail_div').show();
            }
            else if(slice==5 || slice==6)
            {
                $('.to_whom_type_div').hide();
                $('#to_whom_type').val('');
                $('.to_whom_div').empty();

                $('.from_whom_type_div').hide();
                $('#from_whom_type').val('');
                $('.from_whom_div').empty();

                $('.total_amount_div').hide();
                $('.amount_div').show();
                $('.due_div').hide();
                $('.transaction_detail_div').hide();
            }
        });

        $(document).on('change', '#to_whom_type', function ()
        {
            var type = $(this).val();
            if(type>0)
            {
                var url = "";
                if (type == 2)
                {
                    url = "{{ route('ajax.supplier_select') }}";
                }
                else if (type == 3)
                {
                    url = "{{ route('ajax.employee_select') }}";
                }
                else
                {
                    url = "{{ route('ajax.customer_select') }}";
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: "JSON",
                    success: function (data, status) {
                        $('.to_whom_div').empty();
                        $('.to_whom_div').html(data);
                        $('.to_whom_div').show();

                        $('.employee_customer_supplier').attr('name', 'to_whom');
                        $('.employee_customer_supplier').attr('id', 'to_whom');
                    },
                    error: function (xhr, desc, err) {
                        console.log("error");
                    }
                });
            }
            else
            {
                $('.to_whom_div').empty();
                $('.to_whom_div').hide();
            }
        });

        $(document).on('change', '#from_whom_type', function ()
        {
            var type = $(this).val();
            if(type>0)
            {
                var url = "";
                if (type == 2)
                {
                    url = "{{ route('ajax.supplier_select') }}";
                }
                else if (type == 3)
                {
                    url = "{{ route('ajax.employee_select') }}";
                }
                else
                {
                    url = "{{ route('ajax.customer_select') }}";
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: "JSON",
                    success: function (data, status) {
                        $('.from_whom_div').empty();
                        $('.from_whom_div').html(data);
                        $('.from_whom_div').show();

                        $('.employee_customer_supplier').attr('name', 'from_whom');
                        $('.employee_customer_supplier').attr('id', 'from_whom');
                    },
                    error: function (xhr, desc, err) {
                        console.log("error");
                    }
                });
            }
            else
            {
                $('.from_whom_div').empty();
                $('.from_whom_div').hide();
            }
        });

        $(document).on('keyup', '.amount', function ()
        {
            var amount = parseFloat($(this).val());
            var total_amount = parseFloat($('.total_amount').val());
            var due = total_amount - amount;

            $('.due').val(due);
        });
    });
</script>