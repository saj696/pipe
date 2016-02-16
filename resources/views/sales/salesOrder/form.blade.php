{!! csrf_field() !!}

<div class="form-group{{ $errors->has('customer_type') ? ' has-error' : '' }}">
    {{ Form::label('customer_type', 'Customer Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('customer_type', Config::get('common.sales_customer_type'), null,['class'=>'form-control','id'=>'sales_customer_type']) }}
        @if ($errors->has('customer_type'))
            <span class="help-block">
                <strong>{{ $errors->first('customer_type') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="customer_id">
    <div class="form-group{{ $errors->has('customer_id') ? ' has-error' : '' }}">
        {{ Form::label('customer_id', 'Customer', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7">
            {{ Form::select('customer_id', $customers, null,['class'=>'form-control','placeholder'=>'Select']) }}
            @if ($errors->has('customer_id'))
                <span class="help-block">
                <strong>{{ $errors->first('customer_id') }}</strong>
            </span>
            @endif
        </div>
    </div>
</div>

<div class="form-group">
    <div class="col-md-offset-5 col-md-3">
        {{ Form::text('product', null,['class'=>'form-control','id'=>'product','placeholder'=>'Search Product']) }}
    </div>
</div>

<div class="product_list">

</div>

<div class="form-group{{ $errors->has('total') ? ' has-error' : '' }}">
    {{ Form::label('total', 'Total Amount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('total', null,['class'=>'form-control']) }}
        @if ($errors->has('total'))
            <span class="help-block">
                <strong>{{ $errors->first('total') }}</strong>
            </span>
        @endif
    </div>
</div>


<div class="form-group{{ $errors->has('discount') ? ' has-error' : '' }}">
    {{ Form::label('discount', 'Discount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('discount', null,['class'=>'form-control']) }}
        @if ($errors->has('discount'))
            <span class="help-block">
                <strong>{{ $errors->first('discount') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('transport_cost') ? ' has-error' : '' }}">
    {{ Form::label('transport_cost', 'Transport Cost', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('transport_cost', null,['class'=>'form-control']) }}
        @if ($errors->has('transport_cost'))
            <span class="help-block">
                <strong>{{ $errors->first('transport_cost') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group{{ $errors->has('paid') ? ' has-error' : '' }}">
    {{ Form::label('paid', 'Paid Amount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('paid', null,['class'=>'form-control']) }}
        @if ($errors->has('paid'))
            <span class="help-block">
                <strong>{{ $errors->first('paid') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group{{ $errors->has('due') ? ' has-error' : '' }}">
    {{ Form::label('due', 'Due', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('due', null,['class'=>'form-control']) }}
        @if ($errors->has('due'))
            <span class="help-block">
                <strong>{{ $errors->first('due') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('remarks') ? ' has-error' : '' }}">
    {{ Form::label('remarks', 'Remarks', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::textarea('remarks', null,['class'=>'form-control', 'rows'=>'3']) }}
        @if ($errors->has('remarks'))
            <span class="help-block">
                <strong>{{ $errors->first('remarks') }}</strong>
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

<script>
    $(document).ready(function () {
        $(document).on('change', '#sales_customer_type', function () {
            var type = $(this).val();
            var url = "";
            if (type == 2) {
                url = "{{ route('ajax.supplier_select') }}";
            } else if (type == 2) {
                url = "{{ route('ajax.employee_select') }}";
            } else {
                url = "{{ route('ajax.customer_select') }}";
            }

            $.ajax({
                url: url,
                type: 'POST',
                dataType: "JSON",
                success: function (data, status) {
                    $('.customer_id').empty();
                    $('.customer_id').html(data);
                },
                error: function (xhr, desc, err) {
                    console.log("error");
                }
            });
        });

        $("#product").autocomplete({
            source: function (request, response) {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('ajax.product_select') }}",
                    dataType: "json",
                    data: {
                        q: request.term
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
//            minLength: 3,
            select: function (event, ui) {
                //console.log(ui);

                var html = "<div class='form-group single_product'>" +
                        "<div class='col-md-offset-3 col-md-2'>" +
                        " <input type='text' class='form-control' value='" + ui.item.label + "' disabled>" +
                        "<input class='product_id' type='hidden' value='" + ui.item.value + "' name='product_id'> " +
                        "</div>" +
                        "<div class='col-md-2'>" +
                        "<input type='text' class='form-control' name='sales_quantity' placeholder='Sales Quantity'> " +
                        "</div>" +
                        "<div class='col-md-2'>" +
                        "<input type='text' class='form-control' name='unit_price' placeholder='Unit Price'> " +
                        "</div>" +
                        "<div class='col-md-1'>" +
                        "<span class='btn btn-danger remove_product'>X</span>" +
                        "</div>" +
                        "</div>";
                var status = true;
                $.each($('.product_list').find('.product_id'), function (index, element) {
                    if (parseInt(element.value) == ui.item.value) {
                        status = false;
                        alert('This product already assigned.')
                        return false;
                    }
                });
                if (status) {
                    $('.product_list').append(html);
                }
                $(this).val('');
                return false;
            },
            open: function () {
                $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            },
            close: function () {
                $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            }
        });

        $(document).on('click', '.remove_product', function () {
            $(this).closest('.single_product').remove();
        });

    })
</script>