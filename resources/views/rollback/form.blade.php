{!! csrf_field() !!}

<div class="form-group">
    {{ Form::label('account_from', 'Account', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('account_from') ? ' has-error' : '' }}">
        {{ Form::select('account_from', $accounts_from, null,['class'=>'form-control', 'placeholder'=>'select', 'id'=>'account_from']) }}
        @if ($errors->has('account_from'))
            <span class="help-block">
                <strong>{{ $errors->first('account_from') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('total_amount', 'Total Amount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('total_amount') ? ' has-error' : '' }}">
        {{ Form::text('total_amount', null,['class'=>'form-control total_amount']) }}
    </div>
</div>

<div class="form-group">
    {{ Form::label('amount', 'Remaining Amount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('amount') ? ' has-error' : '' }}">
        {{ Form::text('amount', null,['class'=>'form-control amount']) }}
        @if ($errors->has('amount'))
            <span class="help-block">
                <strong>{{ $errors->first('amount') }}</strong>
            </span>
        @endif
    </div>
</div>

{{--<div class="form-group{{ $errors->has('account_to') ? ' has-error' : '' }}">--}}
{{--{{ Form::label('account_to', 'Adjustment Account', ['class'=>'col-md-3 control-label']) }}--}}
{{--<div class="col-md-7">--}}
{{--{{ Form::select('account_to', $accounts_to, null,['class'=>'form-control', 'placeholder'=>'select', 'id'=>'account_to']) }}--}}
{{--@if ($errors->has('account_to'))--}}
{{--<span class="help-block">--}}
{{--<strong>{{ $errors->first('account_to') }}</strong>--}}
{{--</span>--}}
{{--@endif--}}
{{--</div>--}}
{{--</div>--}}

<div class="form-actions">
    <div class="row text-center">
        <div class="col-md-12">
            {{ Form::submit($submitText, ['class'=>'btn btn-circle green']) }}
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('change', '#account_from', function () {
            $('.total_amount').val('');
            $('.amount').val('');

            var account_from = $(this).val();
            if (account_from > 0) {
                if (account_from == 25000) {
                    $.ajax({
                        url: '{{ route('ajax.adjustment_amounts') }}',
                        type: 'POST',
                        dataType: "JSON",
                        data: {account: account_from, _token: $('input[name=_token]').val()},
                        success: function (data, status) {
                            var total_amount = (data['total_amount']).toFixed(2);
                            var remaining_amount = (data['remaining_amount']).toFixed(2);
                            $('.total_amount').val(total_amount);
                            $('.amount').val(remaining_amount);
                        },
                        error: function (xhr, desc, err) {
                            console.log("error");
                        }
                    });
                }
                else if (account_from == 27000) {
                    $.ajax({
                        url: '{{ route('ajax.adjustment_amounts') }}',
                        type: 'POST',
                        dataType: "JSON",
                        data: {account: account_from, _token: $('input[name=_token]').val()},
                        success: function (data, status) {
                            var total_amount = (data).toFixed(2);
                            $('.total_amount').val(total_amount);
                        },
                        error: function (xhr, desc, err) {
                            console.log("error");
                        }
                    });
                }
            }
        });
    });
</script>