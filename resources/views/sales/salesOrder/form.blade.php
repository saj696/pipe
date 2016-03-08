{!! csrf_field() !!}

<div class="form-group">
    {{ Form::label('customer_type', 'Customer Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('customer_type') ? ' has-error' : '' }}">
        {{ Form::select('customer_type', Config::get('common.sales_customer_type'), Config::get('common.person_type_customer'),['class'=>'form-control','id'=>'sales_customer_type']) }}
        @if ($errors->has('customer_type'))
            <span class="help-block">
                <strong>{{ $errors->first('customer_type') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="customer_id">
    <div class="form-group">
        {{ Form::label('customer_id', 'Customer', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7{{ $errors->has('customer_id') ? ' has-error' : '' }}">
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
    <div class="col-md-offset-5 col-md-3 {{ $errors->has('product') ? ' has-error' : '' }}">
        {{ Form::text('product', null,['class'=>'form-control','id'=>'product','placeholder'=>'Search Product']) }}
        @if ($errors->has('product'))
            <span class="help-block">
                <strong>{{ $errors->first('product') }}</strong>
            </span>
        @endif
    </div>

</div>

<div class="product_list" data-product-id="0">
    <div class='col-md-offset-1 col-md-2'>
        <label for="">Product Name</label>
    </div>
    <div class='col-md-2'>
        <label for="">Stock Quantity</label>
    </div>
    <div class='col-md-2'>
        <label for="">Sales Quantity</label>
    </div>
    <div class='col-md-2'>
        <label for="">Unit Price</label>
    </div>
    <div class='col-md-2'>
        <label for="">Total</label>
    </div>
</div>

<div class="form-group">
    {{ Form::label('total', 'Total Amount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('total') ? ' has-error' : '' }}">
        {{ Form::text('total', null,['class'=>'form-control']) }}
        @if ($errors->has('total'))
            <span class="help-block">
                <strong>{{ $errors->first('total') }}</strong>
            </span>
        @endif
    </div>
</div>


<div class="form-group">
    {{ Form::label('discount', 'Discount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('discount') ? ' has-error' : '' }}">
        {{ Form::text('discount', null,['class'=>'form-control']) }}
        @if ($errors->has('discount'))
            <span class="help-block">
                <strong>{{ $errors->first('discount') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('transport_cost', 'Transport Cost', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('transport_cost') ? ' has-error' : '' }}">
        {{ Form::text('transport_cost', null,['class'=>'form-control']) }}
        @if ($errors->has('transport_cost'))
            <span class="help-block">
                <strong>{{ $errors->first('transport_cost') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group">
    {{ Form::label('paid', 'Paid Amount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('paid') ? ' has-error' : '' }}">
        {{ Form::text('paid', null,['class'=>'form-control']) }}
        @if ($errors->has('paid'))
            <span class="help-block">
                <strong>{{ $errors->first('paid') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group">
    {{ Form::label('due', 'Due', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('due') ? ' has-error' : '' }}">
        {{ Form::text('due', null,['class'=>'form-control']) }}
        @if ($errors->has('due'))
            <span class="help-block">
                <strong>{{ $errors->first('due') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('remarks', 'Remarks', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('remarks') ? ' has-error' : '' }}">
        {{ Form::textarea('remarks', null,['class'=>'form-control', 'rows'=>'3','required']) }}
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
            } else if (type == 1) {
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
                        q: request.term,
                        '_token': $('input[name=_token]').val()
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
//            minLength: 3,
            select: function (event, ui) {
//                console.log(ui);
                var quantity=0;
                if(ui.item.quantity>0)
                {
                    quantity=ui.item.quantity;
                }
                var index = $('.product_list').data('product-id');
                var html = "<div class='form-group single_product'>" +
                        "<div class='col-md-offset-1 col-md-2'>" +
                        " <input type='text' class='form-control' value='" + ui.item.label + "' disabled>" +
                        "<input class='product_id' type='hidden' value='" + ui.item.value + "' name='product[" + index + "][product_id]'> " +
                        "</div>" +
                        "<div class='col-md-2'>" +
                        "<input  class='form-control' disabled value='" + quantity + "'> " +
                        "</div>" +
                        "<div class='col-md-2'>" +
                        "<input required type='text' class='form-control pcal single_p_quantity' name='product[" + index + "][sales_quantity]' placeholder='Sales Quantity'> " +
                        "</div>" +
                        "<div class='col-md-2'>" +
                        "<input required title='W: " + ui.item.wholesale_price + ", R: " + ui.item.retail_price + "' type='text' class='form-control pcal single_p_rate' name='product[" + index + "][unit_price]' placeholder='Unit Price'> " +
                        "</div>" +
                        "<div class='col-md-2'>" +
                        "<input  type='text' class='form-control single_p_total'> " +
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
                    $('.product_list').data('product-id', index + 1)
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
            var obj = $(this);
            obj.closest('.single_product').remove();
            findTotal();
            calculateAmount();
        });

        $(document).on('change', '.pcal', function () {
            rowTotal(this);
        });

        $(document).on('change', '#paid', function () {
            var paid = parseFloat($(this).val());
            var due = parseFloat($('#due').val());
            if (paid > due) {
                $(this).val("");
            }
            calculateAmount();
        });

        $(document).on('change', '#discount', function () {
            calculateAmount();
        });

        $(document).on('change', '#transport_cost', function () {
            calculateAmount();
        });


    });

    function rowTotal(ele) {
        var q = $(ele).closest('.single_product').find('.single_p_quantity').val();
        var r = $(ele).closest('.single_product').find('.single_p_rate').val();
        if (q && r) {
            $(ele).closest('.single_product').find('.single_p_total').val((parseFloat(q) * parseFloat(r)).toFixed(2));
            findTotal();
        }

    }
    function findTotal() {
        var total = 0;
        $.each($('.single_p_total'), function () {
            if (this.value)
                total += parseFloat(this.value);
        });
        $('#total').val(total.toFixed(2));
        calculateAmount();
    }

    function calculateAmount() {
        var discount = parseFloat($('#discount').val());
        var t_cost = parseFloat($('#transport_cost').val());
        var paid_amount = parseFloat($('#paid').val());
        var total_amount = parseFloat($('#total').val());
        var due = 0.0;
        if (!discount) {
            discount = 0;
        }
        if (!t_cost) {
            t_cost = 0;
        }
        if (!paid_amount) {
            paid_amount = 0
        }
        due = (total_amount + t_cost) - (paid_amount + discount);
        $('#due').val(due.toFixed(2));
    }
</script>