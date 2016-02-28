{!! csrf_field() !!}

<div class="form-group{{ $errors->has('month') ? ' has-error' : '' }}">
    {{ Form::label('month', 'Month', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
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
            var month = $(this).val();
            var employee_type = {{ Config::get('common.employee_type.Regular') }}
            $.ajax({
                type: 'POST',
                url: '{{ route('ajax.get_employee_payment_list') }}',
                dataType: 'JSON',
                data: {employee_type: employee_type, month: month, '_token': $('input[name=_token]').val()},
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


        $(document).on('submit', 'form', function (e) {
            if (!$('.e_select:checked').length) {
                alert('Please select at least one.')
                e.preventDefault();
            }
        });

        $(document).on('keyup' ,'.pay_now', function () {
            var obj=$(this);
            var pay_now=parseFloat($(this).val());
            var due= parseFloat(obj.closest('tr').find('.due').val());
            if(pay_now>due)
            {
                alert('Pay now could not greater than due amount');
                $(this).val(due);
            }

        });

        $(document).on('change', '#month', function () {
            $('#employee_type').prop('selectedIndex', 0);
        });
    });
</script>