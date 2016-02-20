<div class="form-group">
    {{ Form::label('customer_id', 'Customer', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('customer_id', $customers, null,['class'=>'form-control employee_customer_supplier','placeholder'=>'Select']) }}
    </div>
</div>