{!! csrf_field() !!}
<div class="form-group{{ $errors->has('company_name') ? ' has-error' : '' }}">
    {{ Form::label('company_name', 'Company name', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('company_name', null,['class'=>'form-control']) }}
        @if ($errors->has('company_name'))
            <span class="help-block">
                <strong>{{ $errors->first('company_name') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group{{ $errors->has('suppliers_type') ? ' has-error' : '' }}">
    {{ Form::label('suppliers_type', 'Supplier Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('suppliers_type',Config::get('common.supplier_types'), null,['class'=>'form-control']) }}
        @if ($errors->has('suppliers_type'))
            <span class="help-block">
                <strong>{{ $errors->first('suppliers_type') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('company_address') ? ' has-error' : '' }}">
    {{ Form::label('company_address', 'Company Address', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::textarea('company_address',null,['class'=>'form-control','rows'=>2]) }}
        @if ($errors->has('company_address'))
            <span class="help-block">
                <strong>{{ $errors->first('company_address') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group{{ $errors->has('company_office_phone') ? ' has-error' : '' }}">
    {{ Form::label('company_office_phone', 'Company Office Phone', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('company_office_phone',null,['class'=>'form-control']) }}
        @if ($errors->has('company_office_phone'))
            <span class="help-block">
                <strong>{{ $errors->first('company_office_phone') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group{{ $errors->has('company_office_fax') ? ' has-error' : '' }}">
    {{ Form::label('company_office_fax', 'Company Office Fax', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('company_office_fax',null,['class'=>'form-control']) }}
        @if ($errors->has('company_office_fax'))
            <span class="help-block">
                <strong>{{ $errors->first('company_office_fax') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group{{ $errors->has('contact_person') ? ' has-error' : '' }}">
    {{ Form::label('contact_person', 'Contact Person', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('contact_person',null,['class'=>'form-control']) }}
        @if ($errors->has('contact_person'))
            <span class="help-block">
                <strong>{{ $errors->first('contact_person') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group{{ $errors->has('contact_person_phone') ? ' has-error' : '' }}">
    {{ Form::label('contact_person_phone', 'Contact Person Phone', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('contact_person_phone',null,['class'=>'form-control']) }}
        @if ($errors->has('contact_person_phone'))
            <span class="help-block">
                <strong>{{ $errors->first('contact_person_phone') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group{{ $errors->has('supplier_description') ? ' has-error' : '' }}">
    {{ Form::label('supplier_description', 'Supplier Description', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::textarea('supplier_description',null,['class'=>'form-control','rows'=>2]) }}
        @if ($errors->has('supplier_description'))
            <span class="help-block">
                <strong>{{ $errors->first('supplier_description') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
    {{ Form::label('status', 'Status', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('status',Config::get('common.status'),null,['class'=>'form-control']) }}
        @if ($errors->has('status'))
            <span class="help-block">
                <strong>{{ $errors->first('status') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
        {{ Form::submit($submitText, ['class'=>'btn green']) }}
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function ()
    {

    });

</script>