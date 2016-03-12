{!! csrf_field() !!}

<div class="form-group">
    {{ Form::label('year', 'Year', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('year') ? ' has-error' : '' }}">
        {{ Form::text('year', null,['class'=>'form-control']) }}
        @if ($errors->has('year'))
            <span class="help-block">
                <strong>{{ $errors->first('year') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3">Start Date</label>
    <div class="col-md-7{{ $errors->has('start_date') ? ' has-error' : '' }}">
        <input name="start_date" type="text" value="" size="16"
               class="form-control from_datepicker">
        @if ($errors->has('start_date'))
            <span class="help-block">
                <strong>{{ $errors->first('start_date') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3">End Date</label>
    <div class="col-md-7{{ $errors->has('end_date') ? ' has-error' : '' }}">
        <input name="end_date" type="text" value="" size="16"
               class="form-control to_datepicker">
        @if ($errors->has('end_date'))
            <span class="help-block">
                <strong>{{ $errors->first('end_date') }}</strong>
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

<script>
    $(document).ready(function () {

        $('.from_datepicker').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            onClose: function (selectedDate) {
                $(".to_datepicker").datepicker("option", "minDate", selectedDate);
            }
        });

        $('.to_datepicker').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            onClose: function (selectedDate) {
                $(".from_datepicker").datepicker("option", "maxDate", selectedDate);
            }
        });

    });
</script>