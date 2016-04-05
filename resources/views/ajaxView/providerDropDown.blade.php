<div class="form-group">
    {{ Form::label('customer_id', 'Service Provider', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('customer_id', $providers, null,['class'=>'form-control select2me employee_customer_supplier','placeholder'=>'Select']) }}
        <div class="error"></div>
    </div>

</div>