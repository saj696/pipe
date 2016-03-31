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
        $(document).on('change', '#month', function () {
            var month=$(this).val();
            var type=1;
            $.ajax({
                type: 'POST',
                url: '{{ route("ajax.get_employee_list") }}',
                dataType: 'JSON',
                data: {month: month, '_token': $('input[name=_token]').val()},
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

        $(document).on('keyup', '.cut', function () {
            var obj = $(this);
            var cut = parseFloat($(this).val());
            var salary = parseFloat(obj.closest('tr').find('.salary').val());
            if (cut >= 0) {
                obj.closest('tr').find('.net').val(salary-cut);
            }
        });

        $(document).on('change', '.over_time', function () {
            var obj = $(this);
            var over_time = parseFloat($(this).val());
            var over_time_rate = parseFloat($(this).data('hourly_rate'));
            if ((over_time * over_time_rate) >= 0) {
                var total = over_time * over_time_rate;
                obj.closest('tr').find('.over_time_amount').val(total)
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

</script>