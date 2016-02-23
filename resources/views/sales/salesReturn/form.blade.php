{!! csrf_field() !!}
<div class="form-group{{ $errors->has('customer_type') ? ' has-error' : '' }}">
    {{ Form::label('customer_type', 'Customer Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('customer_type', Config::get('common.sales_customer_type'), Config::get('common.person_type_customer'),['class'=>'form-control','id'=>'sales_customer_type',]) }}
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

<div class="form-group{{ $errors->has('product') ? ' has-error' : '' }}">
    <div class="col-md-offset-5 col-md-3">
        {{ Form::text('product', null,['class'=>'form-control','id'=>'product','placeholder'=>'Search Product']) }}
        @if ($errors->has('product'))
            <span class="help-block">
                <strong>{{ $errors->first('product') }}</strong>
            </span>
        @endif
    </div>

</div>

<div class="product_list" data-product-id="0">
    <div class='col-md-offset-3 col-md-2'>
        <label for="">Product Name</label>
    </div>
    <div class='col-md-2'>
        <label for="">Quantity Returned</label>
    </div>
    <div class='col-md-2'>
        <label for="">Price/Unit</label>
    </div>
    <div class='col-md-2'>
        <label for="">Total</label>
    </div>
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

<div class="form-group{{ $errors->has('return_type') ? ' has-error' : '' }}">
    {{ Form::label('return_type', 'Return Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('return_type', Config::get('common.product_return_type'), null,['class'=>'form-control','id'=>'product_return_type','placeholder'=>'Select']) }}
        @if ($errors->has('return_type'))
            <span class="help-block">
                <strong>{{ $errors->first('return_type') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="pay_due">

</div>

<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-5 col-md-7">
            {{ Form::submit('Save', ['class'=>'btn green']) }}
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
                        q: request.term
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
//            minLength: 3,
            select: function (event, ui) {
//                console.log(ui);
                var index = $('.product_list').data('product-id');
                var html = "<div class='form-group single_product'>" +
                        "<div class='col-md-offset-3 col-md-2'>" +
                        " <input type='text' class='form-control' value='" + ui.item.label + "' disabled>" +
                        "<input class='product_id' type='hidden' value='" + ui.item.value + "' name='product[" + index + "][product_id]'> " +
                        "</div>" +
                        "<div class='col-md-2'>" +
                        "<input required type='text' class='form-control pcal single_p_quantity' name='product[" + index + "][quantity_returned]' placeholder='Sales Quantity'> " +
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
        });

        $(document).on('change', '.pcal', function () {
            rowTotal(this);
        });

        $(document).on('change', '#product_return_type', function () {
            var return_type = $(this).val();
            if (return_type == 2 || return_type == 4) {
                $.ajax({
                    url: '{{ route('ajax.get_person_due_amount') }}',
                    type: 'POST',
                    dataType: "JSON",
                    data: {
                        person_id: $('#customer_id option:selected').val(),
                        person_type: $('#sales_customer_type option:selected').val()
                    },
                    success: function (data, status) {
                        var name = $('#customer_id option:selected').text()
                        var html = '<div class="col-md-offset-3 col-md-7">' +
                                '<div class="alert alert-success">' +
                                name + ' has due ' + data +
                                '</div>' +
                                '</div>' +
                                '<div class="form-group">' +
                                '<label class="col-md-3 control-label" for="total">Due Paid</label>' +
                                '<div class="col-md-7">' +
                                '<input type="text" id="due_paid" name="due_paid" class="form-control" required>' +
                                '</div>' +
                                '</div>';
                        $('.pay_due').html(html);
                    },
                    error: function (xhr, desc, err) {

                    }
                })
            }else{
                $('.pay_due').html("");
            }
        });


    });

    function rowTotal(ele) {
        var q = $(ele).closest('.single_product').find('.single_p_quantity').val();
        var r = $(ele).closest('.single_product').find('.single_p_rate').val();
        if (q && r) {
            $(ele).closest('.single_product').find('.single_p_total').val(parseFloat(q) * parseFloat(r));
            findTotal();
        }

    }
    function findTotal() {
        var total = 0;
        $.each($('.single_p_total'), function () {
            if (this.value)
                total += parseFloat(this.value);
        });
        $('#total').val(total);
        calculateAmount();
    }

</script>