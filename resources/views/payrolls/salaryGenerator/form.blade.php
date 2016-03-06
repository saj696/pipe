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
    {{ Form::label('employee_type', 'Employee Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('employee_type') ? ' has-error' : '' }}">
        {{ Form::select('employee_type',array_flip(Config::get('common.employee_type')), null,['class'=>'form-control','placeholder'=>'Select','required']) }}
        @if ($errors->has('employee_type'))
            <span class="help-block">
                <strong>{{ $errors->first('employee_type') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="employee_list">

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
        $(document).on('change', '#employee_type', function () {
            var type = $(this).val();
            var month = $('#month option:selected').val();
            if (type && month) {
                var url = '{{ route('ajax.get_employee_list') }}';
            } else {
                $(this).prop('selectedIndex', 0);
                alert('Please Select Both Month & Employee Type.');
            }
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'JSON',
                data: {employee_type: type, month: month, '_token': $('input[name=_token]').val()},
                success: function (data, status) {
                    $('.employee_list').html(data);
                },
                error: function (xhr, desc, err) {
                    $('.employee_list').html("");
                }
            });
        });

        $(document).on('click', '#check_all', function () {
            $(".e_select").prop('checked', $(this).prop("checked"));
        });

        $(document).on('change', '.cut', function () {
            var obj = $(this);
            var cut = parseFloat($(this).val());
            if (cut >= 0) {
                obj.closest('tr').find('.net').val(calculation(obj));
            }
        });

        $(document).on('change', '.over_time', function () {
            var obj = $(this);
            var over_time = parseFloat($(this).val());
            var over_time_rate = parseFloat($(this).data('hourly_rate'));
            if ((over_time * over_time_rate) > 0) {
                var total = over_time * over_time_rate;
                obj.closest('tr').find('.over_time_amount').val(total)
            }
            obj.closest('tr').find('.net').val(calculation(obj));
        });

        $(document).on('change', '.bonus', function () {
            var obj = $(this);
            var bonus = parseFloat($(this).val());
            if (bonus > 0) {
                obj.closest('tr').find('.net').val(calculation(obj));
            }
        });

        $(document).on('submit', 'form', function (e) {
            if (!$('.e_select:checked').length) {
                alert('Please select at least one.')
                e.preventDefault();
            }
        });

        $(document).on('change', '#month', function () {
            $('#employee_type').prop('selectedIndex', 0);
        });
    });

    function calculation(obj) {
        var extra_salary = parseFloat(obj.closest('tr').find('.over_time_amount').val());
        console.log(extra_salary);
        if (isNaN(extra_salary))
            extra_salary = 0;
        var cut = parseFloat(obj.closest('tr').find('.cut').val());
        if (isNaN(cut))
            cut = 0;
        var bonus = parseFloat(obj.closest('tr').find('.bonus').val());
        if (isNaN(bonus))
            bonus = 0;
        var salary = parseFloat(obj.closest('tr').find('.salary').val());
        return ((salary + extra_salary + bonus) - cut)
    }

</script>