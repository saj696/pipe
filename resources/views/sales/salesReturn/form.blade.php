{!! csrf_field() !!}
<div class="form-group">
    {{ Form::label('customer_type', 'Customer Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('customer_type') ? ' has-error' : '' }}">
        {{ Form::select('customer_type', Config::get('common.sales_customer_type'), Config::get('common.person_type_customer'),['class'=>'form-control','id'=>'sales_customer_type',]) }}
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
            {{ Form::select('customer_id', $customers, null,['class'=>'form-control select2me','placeholder'=>'Select','required']) }}
            @if ($errors->has('customer_id'))
                <span class="help-block">
                <strong>{{ $errors->first('customer_id') }}</strong>
            </span>
            @endif
        </div>
    </div>
</div>

<div class="form-group">
    <div class="col-md-offset-5 col-md-3{{ $errors->has('product') ? ' has-error' : '' }}">
        {{ Form::text('product', null,['class'=>'form-control','id'=>'product','placeholder'=>'Search Product']) }}
        @if ($errors->has('product'))
            <span class="help-block">
                <strong>{{ $errors->first('product') }}</strong>
            </span>
        @endif
    </div>

</div>

<div class="product_list" data-product-id="0">
    <div class="form-group">
        <div class='col-md-offset-2 col-md-2'>
            <label for="">Product Name</label>
        </div>
        <div class='col-md-3'>
            <label for="">Return Quantity</label>
        </div>
        <div class='col-md-2'>
            <label for="">Unit Price</label>
        </div>
        <div class='col-md-2'>
            <label for="">Total</label>
        </div>
        <div class='col-md-1'>
            <label for=""></label>
        </div>
    </div>
</div>

<div class="form-group">
    {{ Form::label('total', 'Total Amount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('total') ? ' has-error' : '' }}">
        {{ Form::text('total', null,['class'=>'form-control','readonly']) }}
        @if ($errors->has('total'))
            <span class="help-block">
                <strong>{{ $errors->first('total') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('return_type', 'Return Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('return_type') ? ' has-error' : '' }}">
        {{ Form::select('return_type', Config::get('common.product_return_type'), null,['class'=>'form-control','id'=>'product_return_type','placeholder'=>'Select','required']) }}
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
            } else if (type == 3){
                url = "{{ route('ajax.customer_select') }}";
            } else if (type == 4){
                url = "{{ route('ajax.provider_select') }}";
            }

            $.ajax({
                url: url,
                type: 'POST',
                dataType: "JSON",
                success: function (data, status) {
                    $('.customer_id').empty();
                    $('.customer_id').html(data);
                    $('.select2me').select2();
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
                var quantity = 0;
                if (ui.item.quantity > 0) {
                    quantity = ui.item.quantity;
                }
                var index = $('.product_list').data('product-id');
                var html = "<div class='form-group single_product'>" +
                        "<div class='col-md-offset-2 col-md-2'>" +
                        " <input type='text' class='form-control' value='" + ui.item.label + "' disabled>" +
                        "<input class='product_id' type='hidden' value='" + ui.item.value + "' name='product[" + index + "][product_id]'> " +
                        "</div>" +
                        "<div class='col-md-3'>" +
                        "<div style='width: 75%;float: left'>" +
                        "<input required type='number' min='1' step='0.01' class='form-control pcal single_p_quantity' name='product[" + index + "][quantity_returned]' placeholder='Sales Quantity'> " +
                        "</div>" +
                        "<div style='width: 25%;float: left'>" +
                        "<select name='product[" + index + "][unit_type]' class='form-control unit_type' style='padding:5px'> " +
                        "<option value='1'>ft</option>" +
                        "<option value='2'>kg</option>" +
                        "</select>" +
                        "</div>" +
                        "</div>" +
                        "<div class='col-md-2'>" +
                        "<input required data-wholesale_price='" + ui.item.wholesale_price + "' data-retail_price='" + ui.item.retail_price + "' data-length='" + ui.item.length + "' data-weight='" + ui.item.weight + "' title='W: " + (ui.item.wholesale_price / ui.item.length).toFixed(2) + ", R: " + (ui.item.retail_price / ui.item.length).toFixed(2) + "' type='number' min='0.01' step='0.01' class='form-control pcal single_p_rate' name='product[" + index + "][unit_price]' placeholder='Unit Price'> " +
                        "<input value='" + ui.item.length + "' type='hidden' name='product[" + index + "][length]'> " +
                        "<input value='" + ui.item.weight + "' type='hidden' name='product[" + index + "][weight]'> " +
                        "</div>" +
                        "<div class='col-md-2'>" +
                        "<input  type='text' readonly class='form-control single_p_total'> " +
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

        $(document).on('change', '.unit_type', function () {
            var type = parseInt($(this).val());
            var obj = $(this);
            var wholesale_price = parseFloat(obj.closest('.single_product').find('.single_p_rate').data('wholesale_price'));
            var retail_price = parseFloat(obj.closest('.single_product').find('.single_p_rate').data('retail_price'));
            if (type == 2) {
                var weight = parseFloat(obj.closest('.single_product').find('.single_p_rate').data('weight'));
                var title = 'W: ' + (wholesale_price / weight).toFixed(2) + ', R: ' + (retail_price / weight).toFixed(2);
                obj.closest('.single_product').find('.single_p_rate').attr('title', title);
            } else {
                var length = parseFloat(obj.closest('.single_product').find('.single_p_rate').data('length'));
                var title = 'W: ' + (wholesale_price / length).toFixed(2) + ', R: ' + (retail_price / length).toFixed(2);
                obj.closest('.single_product').find('.single_p_rate').attr('title', title);
            }
        });

        $(document).on('change', '#product_return_type', function (e) {
            var return_type = $(this).val();
            if (return_type == 2 || return_type == 4) {

                var person_id = $('#customer_id option:selected').val();
                var total = parseFloat($('#total').val());

                if (person_id <= 0) {
                    $('#product_return_type').prop('selectedIndex',0);
                }


                $.ajax({
                    url: '{{ route('ajax.get_person_due_amount') }}',
                    type: 'POST',
                    dataType: "JSON",
                    data: {
                        person_id: person_id,
                        person_type: $('#sales_customer_type option:selected').val(),
                        '_token': $('input[name=_token]').val()
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
                                '<input type="number" step="0.01" min="0" max="'+ total +'" id="due_paid" name="due_paid" class="form-control" required>' +
                                '</div>' +
                                '</div>';
                        $('.pay_due').html(html);
                        var total_amount = parseFloat($('#total').val());
                        if (return_type == 2 && total_amount > data) {
                            alert('Total amount cannot be greater than Due amount. Please select the Return type "Pay Due & Cash Return"');
                            $('#product_return_type').prop('selectedIndex',0);
                        }
                    },
                    error: function (xhr, desc, err) {

                    }
                })
            } else {
                $('.pay_due').html("");
            }
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
//        calculateAmount();
    }

</script>