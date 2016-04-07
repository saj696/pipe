{!! csrf_field() !!}

<div class="form-group">
    {{ Form::label('date', 'Date', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('date') ? ' has-error' : '' }}">
        {{ Form::text('date', null,['class'=>'form-control transaction_date']) }}
        @if ($errors->has('date'))
            <span class="help-block">
                <strong>{{ $errors->first('date') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('account_code', 'Account', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('account_code') ? ' has-error' : '' }}">
        {{ Form::select('account_code', $accounts, null,['class'=>'form-control', 'id'=>'account_type', 'placeholder'=>'Select']) }}
        @if ($errors->has('account_code'))
            <span class="help-block">
                <strong>{{ $errors->first('account_code') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group cash_adjustment_type" style="display: {{ $errors->has('cash_adjustment_type')?'show':'none' }};">
    {{ Form::label('cash_adjustment_type', 'Cash Adjustment Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('cash_adjustment_type') ? ' has-error' : '' }}">
        {{ Form::select('cash_adjustment_type', array_flip(Config::get('common.cash_adjustment_type')), null,['class'=>'form-control', 'id'=>'cash_adjustment_type', 'placeholder'=>'Select']) }}
        @if ($errors->has('cash_adjustment_type'))
            <span class="help-block">
                <strong>{{ $errors->first('cash_adjustment_type') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="to_whom_type_div form-group"
     style="display: {{ (isset($recorder->to_whom_type) && $recorder->to_whom_type>0)?'show':'none' }};">
    {{ Form::label('to_whom_type', 'To Whom Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('to_whom_type') ? ' has-error' : '' }}">
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
        @elseif($recorder->to_whom_type==3)
            <?php
            $to_whom_data = $providers;
            $label = 'Service Provider';
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

<div class="from_whom_type_div form-group"
     style="display: {{ (isset($recorder->from_whom_type) && $recorder->from_whom_type>0)?'show':'none' }};">
    {{ Form::label('from_whom_type', 'From Whom Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('from_whom_type') ? ' has-error' : '' }}">
        {{ Form::select('from_whom_type', $types, null,['class'=>'form-control', 'id'=>'from_whom_type', 'placeholder'=>'Select']) }}
        @if ($errors->has('from_whom_type'))
            <span class="help-block">
                <strong>{{ $errors->first('from_whom_type') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="from_whom_div"
     style="display: {{ (isset($recorder->from_whom_type) && $recorder->from_whom_type>0)?'show':'none' }};">
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
        @elseif($recorder->from_whom_type==4)
            <?php
            $from_whom_data = $providers;
            $label = 'Service Provider';
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

<div class="total_amount_div form-group"
     style="display: {{ (isset($recorder->total_amount) && $recorder->total_amount>0)?'show':'none' }};">
    {{ Form::label('total_amount', 'Total Amount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('total_amount') ? ' has-error' : '' }}">
        {{ Form::text('total_amount', null,['class'=>'form-control total_amount quantity']) }}
        @if ($errors->has('total_amount'))
            <span class="help-block">
                <strong>{{ $errors->first('total_amount') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="amount_div form-group"
     style="display: {{ (isset($recorder->amount) && $recorder->amount>0)?'show':'none' }};">
    {{ Form::label('amount', 'Amount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('amount') ? ' has-error' : '' }}">
        {{ Form::text('amount', null,['class'=>'form-control amount quantity', 'required'=>'required']) }}
        @if ($errors->has('amount'))
            <span class="help-block">
                <strong>{{ $errors->first('amount') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group due_div"
     style="display: {{ (isset($recorder->total_amount) && $recorder->total_amount>0)?'show':'none' }};">
    {{ Form::label('due', 'Due', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('due', (isset($recorder->total_amount) && isset($recorder->amount))?($recorder->total_amount - $recorder->amount):null,['class'=>'form-control due']) }}
    </div>
</div>

<div class="transaction_detail_div form-group"
     style="display: {{ (isset($recorder->transaction_detail) && strlen($recorder->transaction_detail)>0)?'show':'none' }};">
    {{ Form::label('transaction_detail', 'Transaction Detail', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('transaction_detail') ? ' has-error' : '' }}">
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

    $(function () {
        $(".transaction_date").datepicker({maxDate: new Date});
    });

    $(document).ready(function () {
        $(document).on("keyup", ".quantity", function () {
            this.value = this.value.replace(/[^0-9\.]/g, '');
        });

        $(document).on('change', '#account_type', function () {
            $('.total_amount').val('');
            var code = $(this).val();
            var slice = code.substring(0, 1);

            if (slice == 1 || slice == 2 || slice == 3) {
                $('.from_whom_type_div').show();
                $('.to_whom_type_div').hide();
                $('#to_whom_type').val('');
                $('.to_whom_div').empty();

                $('.total_amount_div').show();
                $('.amount_div').show();
                $('.due_div').show();
                $('.transaction_detail_div').show();
            }
            else if (slice == 4) {
                $('.to_whom_type_div').show();
                $('.from_whom_type_div').hide();
                $('#from_whom_type').val('');
                $('.from_whom_div').empty();

                $('.total_amount_div').show();
                $('.amount_div').show();
                $('.due_div').show();
                $('.transaction_detail_div').show();
            }
            else if (slice == 5 || slice == 6) {
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

            // Donation & Jakat
            if(code==29930 || code==29960)
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
            }

            if(code==29940)
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
                $('.cash_adjustment_type').show();
            }
            else
            {
                $('.cash_adjustment_type').hide();
                $('#cash_adjustment_type').val('');
            }

            if(code==12000 || code==41000)
            {
                $('.total_amount').attr('readonly','readonly');
            }
            else
            {
                $('.total_amount').removeAttr('readonly');
            }
        });

        $(document).on('change', '#to_whom_type', function () {
            $('.total_amount').val('');
            var type = $(this).val();
            if (type > 0) {
                var url = "";
                if (type == 2) {
                    url = "{{ route('ajax.supplier_select') }}";
                }
                else if (type == 1) {
                    url = "{{ route('ajax.employee_select') }}";
                }
                else if (type == 3) {
                    url = "{{ route('ajax.customer_select') }}";
                }
                else if (type == 4) {
                    url = "{{ route('ajax.provider_select') }}";
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
                        $('.select2me').select2();
                    },
                    error: function (xhr, desc, err) {
                        console.log("error");
                    }
                });
            }
            else {
                $('.to_whom_div').empty();
                $('.to_whom_div').hide();
            }
        });

        $(document).on('change', '#from_whom_type', function () {
            $('.total_amount').val('');
            var type = $(this).val();
            if (type > 0) {
                var url = "";
                if (type == 2) {
                    url = "{{ route('ajax.supplier_select') }}";
                }
                else if (type == 1) {
                    url = "{{ route('ajax.employee_select') }}";
                }
                else if (type == 3) {
                    url = "{{ route('ajax.customer_select') }}";
                }
                else if (type == 4) {
                    url = "{{ route('ajax.provider_select') }}";
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
                        $('.select2me').select2();
                    },
                    error: function (xhr, desc, err) {
                        console.log("error");
                    }
                });
            }
            else {
                $('.from_whom_div').empty();
                $('.from_whom_div').hide();
            }
        });

        $(document).on('keyup', '.amount', function () {
            var amount = parseFloat($(this).val());
            var total_amount = parseFloat($('.total_amount').val());
            var due = total_amount - amount;

            if(due<0)
            {
                $(this).val(0);
                $('.due').val(total_amount);
                alert('Paid amount exceeds total amount!');
            }
            else
            {
                $('.due').val(due);
            }
        });

        $(document).on('change', '#from_whom', function () {
            var code = $('#account_type').val();
            var slice = code.substring(0, 1);
            var type = $('#from_whom_type').val();
            var person_id = $(this).val();

            if ($(this).val() > 0 && slice == 1) {
                $('.total_amount').val('');
                $.ajax({
                    url: '{{ route('ajax.transaction_recorder_amount') }}',
                    type: 'POST',
                    dataType: "JSON",
                    data: {type: type, slice: slice, person_id: person_id},
                    success: function (data, status) {
                        $('.total_amount').val(data);
                    },
                    error: function (xhr, desc, err) {
                        console.log("error");
                    }
                });
            }
        });

        $(document).on('change', '#to_whom', function () {
            var code = $('#account_type').val();
            var slice = code.substring(0, 1);
            var type = $('#to_whom_type').val();
            var person_id = $(this).val();

            if ($(this).val() > 0 && slice == 4) {
                $('.total_amount').val('');
                $.ajax({
                    url: '{{ route('ajax.transaction_recorder_amount') }}',
                    type: 'POST',
                    dataType: "JSON",
                    data: {type: type, slice: slice, person_id: person_id},
                    success: function (data, status) {
                        $('.total_amount').val(data);
                    },
                    error: function (xhr, desc, err) {
                        console.log("error");
                    }
                });
            }
        });

        $("form").submit(function( event ) {
            var code = $('#account_type').val();
            var slice = code.substring(0, 1);

            if ((slice == 1 || slice == 2 || slice == 3) && code!=29930 && code!=29960 && code!=29940)
            {
                if($('#from_whom').val()>0)
                {

                }
                else
                {
                    alert("From whom required!");
                    event.preventDefault();
                }
            }
            else if (slice == 4)
            {
                if($('#to_whom').val()>0)
                {

                }
                else
                {
                    alert("To whom required!");
                    event.preventDefault();
                }
            }
        });
    });
</script>