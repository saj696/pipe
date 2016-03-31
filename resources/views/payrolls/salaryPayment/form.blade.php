{!! csrf_field() !!}

<div class="form-group">
    {{ Form::label('month', 'Month', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('month') ? ' has-error' : '' }}">
        {{ Form::select('month', array_flip(Config::get('common.month')), null,['class'=>'form-control','placeholder'=>'Select','required']) }}
        @if ($errors->has('month'))
            <span class="help-block">
                <strong>{{ $errors->first('month') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    <div class="col-md-offset-4 col-md-4">
        {{ Form::text('', null,['class'=>'form-control','placeholder'=>'Search Employee','id'=>'employee']) }}

    </div>
</div>

<div class="employee_info">

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
        $("#employee").autocomplete({
            source: function (request, response) {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('ajax.get_employee') }}",
                    dataType: "json",
                    data: {
                        q: request.term,
                        '_token': $('input[name=_token]').val()
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
//            minLength: 3,
            select: function (event, ui) {
                var employee_id = ui.item.value;
                var month = $('#month').val();
                if (!month) {
                    alert('Please select a month.')
                    $(this).val('');
                    return false;
                }
                $.ajax({
                    type: 'POST',
                    url: '{{ route('ajax.get_employee_payment') }}',
                    data: {employee_id: employee_id, month: month, '_token': $('input[name=_token]').val()},
                    success: function (data, status) {
                        $('.employee_info').html(data)
                    },
                    error: function (xhr, desc, err) {

                    }
                });

                $(this).val('');
                return false;
            },
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });

        $(document).on('keyup', '#pay_now', function () {
            var pay_now = parseFloat($(this).val());
            var net_salary = parseFloat($('#net_salary').val());
            var net_paid = parseFloat($('#net_paid').val());
            if (isNaN(net_salary)) {
                net_salary = 0;
            }
            if (isNaN(net_paid)) {
                net_paid = 0;
            }
            var bonus = parseFloat($('#bonus').val());
            var bonus_paid = parseFloat($('#bonus_paid').val());
            if (isNaN(bonus)) {
                bonus = 0;
            }
            if (isNaN(bonus_paid)) {
                bonus_paid = 0;
            }
            var over_time_amount = parseFloat($('#over_time_amount').val());
            var over_time_paid = parseFloat($('#over_time_paid').val());
            if (isNaN(over_time_amount)) {
                over_time_amount = 0;
            }
            if (isNaN(over_time_paid)) {
                over_time_paid = 0;
            }

            var net_due=net_salary-net_paid;
            var bonus_due=bonus-bonus_paid;
            var overtime_due=over_time_amount-over_time_paid;

            if (net_due > 0 && pay_now >= net_due) {
                $('#net_pay').val(net_due);
                pay_now -= net_due;
            } else if (net_due > 0 && pay_now < net_due) {
                $('#net_pay').val(pay_now);
                pay_now = 0;
            }

            if (bonus_due > 0 && pay_now >= bonus_due) {
                $('#bonus_pay').val(bonus_due);
                pay_now -= bonus_due;
            } else if (bonus > 0 && pay_now < bonus_due) {
                $('#bonus_pay').val(pay_now);
                pay_now = 0;
            }

            if (overtime_due > 0 && pay_now >= overtime_due) {
                $('#over_time_pay').val(overtime_due);
                pay_now -= overtime_due;
            } else if (bonus > 0 && pay_now < overtime_due) {
                $('#over_time_pay').val(pay_now);
                pay_now = 0;
            }

        });

        $(document).on('submit', 'form', function (e) {
            var pay_now = parseFloat($('#pay_now').val());
            var net_pay = parseFloat($('#net_pay').val());
            if (isNaN(net_pay)) {
                net_pay = 0;
            }
            var bonus_pay = parseFloat($('#bonus_pay').val());
            if (isNaN(bonus_pay)) {
                bonus_pay = 0;
            }
            var over_time_pay = parseFloat($('#over_time_pay').val());
            if (isNaN(over_time_pay)) {
                over_time_pay = 0;
            }

            if (pay_now != (net_pay + bonus_pay + over_time_pay)) {
                e.preventDefault();
                alert('Sub accounts payments should not exceed the gross paid amount.')
            }
        });
    });
</script>