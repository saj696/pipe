<div class="form-group">
    {{ Form::label('', 'Payment Date', ['class'=>'col-md-4 control-label']) }}
    <div class="col-md-4">
        {{ Form::text('', date('d-m-Y',$wage->payment_date),['class'=>'form-control','disabled']) }}
    </div>
</div>

<div class="form-group">
    {{ Form::label('', 'Employee Name', ['class'=>'col-md-4 control-label']) }}
    <div class="col-md-4">
        {{ Form::text('', $wage->employee->name,['class'=>'form-control','disabled']) }}
    </div>
</div>

<div class="form-group">
    {{ Form::label('', 'Mobile', ['class'=>'col-md-4 control-label']) }}
    <div class="col-md-4">
        {{ Form::text('', $wage->employee->mobile,['class'=>'form-control','disabled']) }}
    </div>
</div>

<div class="form-group">
    {{ Form::label('wage', 'Wage', ['class'=>'col-md-4 control-label']) }}
    <div class="col-md-4">
        {{ Form::number('wage', $wage->wage,['class'=>'form-control','min'=>$wage->paid, 'step'=>0.01]) }}
    </div>
</div>

<div class="form-group">
    {{ Form::label('', 'Due', ['class'=>'col-md-4 control-label']) }}
    <div class="col-md-4">
        {{ Form::number('', $wage->due,['class'=>'form-control','disabled']) }}
    </div>
</div>

<div class="form-group">
    {{ Form::label('paid', 'Paid', ['class'=>'col-md-4 control-label']) }}
    <div class="col-md-4">
        {{ Form::number('paid', $wage->paid,['class'=>'form-control','min'=>0, 'step'=>0.01, 'max'=>$wage->wage]) }}
    </div>
</div>

<div class="form-actions">
    <div class="row">
        <div class="text-center col-md-12">
            {{ Form::submit($submitText, ['class'=>'btn btn-circle green']) }}
        </div>
    </div>
</div>
