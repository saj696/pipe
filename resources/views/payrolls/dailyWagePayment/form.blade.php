{!! csrf_field() !!}

<div class="form-group">
    {{ Form::label('payment_date', 'Payment Date', ['class'=>'col-md-4 control-label']) }}
    <div class="col-md-4{{ $errors->has('payment_date') ? ' has-error' : '' }}">
        {{ Form::text('payment_date', date('d-m-Y'),['class'=>'form-control hasdatepicker']) }}
        @if ($errors->has('payment_date'))
            <span class="help-block">
                 <strong>{{ $errors->first('payment_date') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="employee_list">
    <table class="table table-bordered">
        <tr>
            <th><input type="checkbox" id="check_all"></th>
            <th>Name</th>
            <th>Mobile</th>
            <th>Wage</th>
            <th>Pay Now</th>
        </tr>
        @if(sizeof($employees)> 0)
            @foreach($employees as $key=>$employee)
                <tr>
                    <td><input type="checkbox" name="selected[{{ $key }}]" class="form-control e_select"
                               value="{{ $employee->id }}"></td>
                    <td width="20%"><input type="text" value="{{ $employee->name }}" disabled
                                           class="form-control"></td>
                    <td><input type="text" value="{{ $employee->mobile }}" disabled
                               class="form-control"></td>
                    <td><input type="number" min="0" step="0.01" name="employee[{{ $employee->id }}][wage]"
                               value="{{ $employee->designation->salary }}"

                               class="form-control wage"></td>
                    <td><input class="form-control pay_now" type="number" min="0" step="0.01"
                               name="employee[{{ $employee->id }}][pay_now]"
                               value="{{ $employee->designation->salary }}"></td>

                    <input type="hidden" name="employee[{{ $employee->id }}][workspace_id]"
                           value="{{ $employee->workspace_id }}">
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="8" style="text-align: center;color: #ff0000">No data found.</td>
            </tr>
        @endif
    </table>
</div>

<div class="form-actions">
    <div class="row">
        <div class="text-center col-md-12">
            {{ Form::submit($submitText, ['class'=>'btn btn-circle green']) }}
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.hasdatepicker').datepicker({
            dateFormat: 'dd-mm-yy',
        })
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

    $(document).on('keyup', '.pay_now', function () {
        var pay_now = parseFloat($(this).val());
        var obj = $(this);
        var wage = parseFloat(obj.closest('tr').find('.wage').val())
        if (pay_now > wage) {
            alert('Paid amount could not greater than Wage amount.')
            $(this).val(wage);
        }
    })

    $(document).on('change', '#payment_date', function () {
        var payment_date = $(this).val();

        $.ajax({
            type: 'POST',
            url: '{{ route('ajax.get_daily_worker_list') }}',
            data: {payment_date: payment_date, '_token': $('input[name=_token]').val()},
            success: function (data, status) {
                $('.employee_list').html(data);
            },
            error: function (xhr, desc, err) {

            }
        });
    });
</script>