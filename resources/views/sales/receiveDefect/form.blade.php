{!! csrf_field() !!}

<div class="customer_type">
    <div class="form-group">
        {{ Form::label('customer_type', 'Customer Type', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7{{ $errors->has('customer_type') ? ' has-error' : '' }}">
            {{ Form::select('customer_type', Config::get('common.sales_customer_type'),3,['class'=>'form-control','id'=>'sales_customer_type','placeholder'=>'Select','oninvalid'=>'this.setCustomValidity("Customer Type Required")']) }}
            <div class="error"></div>
            @if ($errors->has('customer_type'))
                <span class="help-block">
                <strong>{{ $errors->first('customer_type') }}</strong>
            </span>
            @endif
        </div>
    </div>
</div>

<div class="customer_id">
    <div class="form-group">
        {{ Form::label('customer_id', 'Customer', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7{{ $errors->has('customer_id') ? ' has-error' : '' }}">
            {{ Form::select('customer_id', $customers, null,['class'=>'form-control select2me','placeholder'=>'Select']) }}
            <div class="error"></div>
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
    <div class="form-group">
        <div class='col-md-offset-1 col-md-2'>
            <label for="">Product Name</label>
        </div>
        <div class='col-md-3'>
            <label for="">Receive Quantity</label>
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
    {{ Form::label('cash', 'Cash', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('cash') ? ' has-error' : '' }}">
        {{ Form::number('cash', null,['class'=>'form-control','min'=>0,'step'=>0.01]) }}
        @if ($errors->has('cash'))
            <span class="help-block">
                <strong>{{ $errors->first('cash') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="pay_due col-md-offset-3 col-md-7">

</div>

<div class="form-group">
    {{ Form::label('due_paid', 'Due Pay', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('due_paid') ? ' has-error' : '' }}">
        {{ Form::number('due_paid', null,['class'=>'form-control','min'=>0,'step'=>0.01]) }}
        @if ($errors->has('due_paid'))
            <span class="help-block">
                <strong>{{ $errors->first('due_paid') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('due', 'Due', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('due') ? ' has-error' : '' }}">
        {{ Form::number('due', null,['class'=>'form-control','min'=>0,'step'=>0.01]) }}
        @if ($errors->has('due'))
            <span class="help-block">
                <strong>{{ $errors->first('due') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('is_replacement', 'Is Replacement', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('is_replacement') ? ' has-error' : '' }}">
        {{ Form::checkbox('is_replacement',1,false,['form-control']) }}
        @if ($errors->has('is_replacement'))
            <span class="help-block">
                <strong>{{ $errors->first('is_replacement') }}</strong>
            </span>
        @endif
    </div>
</div>


<div class="warp_new_product" style="display: none">
    <div class="form-group">
        <div class="col-md-offset-5 col-md-3 {{ $errors->has('new_product') ? ' has-error' : '' }}">
            {{ Form::text('new_product', null,['class'=>'form-control','id'=>'new_product','placeholder'=>'Search New Product']) }}
            @if ($errors->has('new_product'))
                <span class="help-block">
                <strong>{{ $errors->first('new_product') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="new_product_list" data-new-product-id="0">
        <div class="form-group">
            <div class='col-md-2'>
                <label for="">Product Name</label>
            </div>
            <div class='col-md-2'>
                <label for="">Stock Quantity</label>
            </div>
            <div class='col-md-3'>
                <label for="">Sales Quantity</label>
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
        {{ Form::label('new_total', 'Total Amount', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7{{ $errors->has('new_total') ? ' has-error' : '' }}">
            {{ Form::text('new_total', null,['class'=>'form-control','readonly']) }}
            @if ($errors->has('new_total'))
                <span class="help-block">
                <strong>{{ $errors->first('new_total') }}</strong>
            </span>
            @endif
        </div>
    </div>
</div>


<div class="form-group">
    {{ Form::label('remarks', 'Remarks', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('remarks') ? ' has-error' : '' }}">
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
        <div class="col-md-12 text-center">
            {{ Form::submit($submitText, ['class'=>'btn btn-circle green']) }}
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
            } else if (type == 3) {
                url = "{{ route('ajax.customer_select') }}";
            } else if (type == 4) {
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
                        "<div class='col-md-offset-1 col-md-2'>" +
                        " <input type='text' class='form-control' value='" + ui.item.label + "' disabled>" +
                        "<input class='product_id' type='hidden' value='" + ui.item.value + "' name='product[" + index + "][product_id]'> " +
                        "</div>" +
                        "<div class='col-md-3'>" +
                        "<div style='width: 75%;float: left'>" +
                        "<input required type='number' min='1' step='0.01' class='form-control pcal single_p_quantity' name='product[" + index + "][receive_quantity]' placeholder='Receive Quantity'> " +
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
                        "<input  type='hidden' value='" + ui.item.length + "' name='product[" + index + "][length]'> " +
                        "<input  type='hidden' value='" + ui.item.weight + "' name='product[" + index + "][weight]'> " +
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

        $("#new_product").autocomplete({
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
                var index = $('.new_product_list').data('new-product-id');
                var html = "<div class='form-group single_product'>" +
                        "<div class='col-md-2'>" +
                        " <input type='text' class='form-control' value='" + ui.item.label + "' disabled>" +
                        "<input class='product_id' type='hidden' value='" + ui.item.value + "' name='new_product[" + index + "][product_id]'> " +
                        "</div>" +
                        "<div class='col-md-2'>" +
                        "<input  class='form-control' disabled value='" + quantity + " (ft) / " + ((quantity / ui.item.length) * ui.item.weight).toFixed(1) + " (kg)'> " +
                        "</div>" +
                        "<div class='col-md-3'>" +
                        "<div style='width: 75%;float: left'>" +
                        "<input required type='number' min='1' step='0.01' max='" + quantity + "' class='form-control new_pcal single_new_product_quantity' name='new_product[" + index + "][sales_quantity]' placeholder='Sales Quantity'> " +
                        "</div>" +
                        "<div style='width: 25%;float: left'>" +
                        "<select name='new_product[" + index + "][sales_unit_type]' class='form-control sales_unit_type' style='padding:5px'> " +
                        "<option value='1'>ft</option>" +
                        "<option value='2'>kg</option>" +
                        "</select>" +
                        "</div>" +
                        "</div>" +
                        "<div class='col-md-2'>" +
                        "<input required data-wholesale_price='" + ui.item.wholesale_price + "' data-retail_price='" + ui.item.retail_price + "' data-length='" + ui.item.length + "' data-weight='" + ui.item.weight + "' title='W: " + (ui.item.wholesale_price / ui.item.length).toFixed(2) + ", R: " + (ui.item.retail_price / ui.item.length).toFixed(2) + "' type='number' min='0.01' step='0.01' class='form-control new_pcal single_new_product_rate' name='new_product[" + index + "][unit_price]' placeholder='Unit Price'> " +
                        "</div>" +
                        "<div class='col-md-2'>" +
                        "<input  type='text' readonly class='form-control single_new_product_total'> " +
                        "</div>" +
                        "<div class='col-md-1'>" +
                        "<span class='btn btn-danger remove_product'>X</span>" +
                        "</div>" +
                        "</div>";
                var status = true;
                $.each($('.new_product_list').find('.product_id'), function (index, element) {
                    if (parseInt(element.value) == ui.item.value) {
                        status = false;
                        alert('This product already assigned.')
                        return false;
                    }
                });
                if (status) {
                    $('.new_product_list').data('new-product-id', index + 1)
                    $('.new_product_list').append(html);
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

        $(document).on('change', '.new_pcal', function () {
            rowTotal2(this);
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

        $(document).on('change', '.sales_unit_type', function () {
            var type = parseInt($(this).val());
            var obj = $(this);
            var wholesale_price = parseFloat(obj.closest('.single_product').find('.single_new_product_rate').data('wholesale_price'));
            var retail_price = parseFloat(obj.closest('.single_product').find('.single_new_product_rate').data('retail_price'));
            if (type == 2) {
                var weight = parseFloat(obj.closest('.single_product').find('.single_new_product_rate').data('weight'));
                var title = 'W: ' + (wholesale_price / weight).toFixed(2) + ', R: ' + (retail_price / weight).toFixed(2);
                obj.closest('.single_product').find('.single_new_product_rate').attr('title', title);
            } else {
                var length = parseFloat(obj.closest('.single_product').find('.single_new_product_rate').data('length'));
                var title = 'W: ' + (wholesale_price / length).toFixed(2) + ', R: ' + (retail_price / length).toFixed(2);
                obj.closest('.single_product').find('.single_new_product_rate').attr('title', title);
            }
        });

        $(document).on('submit', 'form', function (e) {
            if ($('#sales_customer_type').val() === "") {
                e.preventDefault();
                $('#sales_customer_type').closest('.customer_type').find('.col-md-7').addClass('has-error')
                var html = '<span class="help-block">' +
                        '<strong>Customer type required.</strong>' +
                        '</span>';
                $('#sales_customer_type').closest('.customer_type').find('.error').html(html)
            }
            if ($('#customer_id').val() === "") {
                e.preventDefault();
                $('#customer_id').closest('.customer_id').find('.col-md-7').addClass('has-error')
                var html = '<span class="help-block">' +
                        '<strong>Customer id required.</strong>' +
                        '</span>';
                $('#customer_id').closest('.customer_id').find('.error').html(html)
            }

            if (!$('#is_replacement').prop('checked')) {
                var total = parseFloat($('#total').val());
                var cash = parseFloat($('#cash').val());
                if (!cash) {
                    cash = 0;
                }
                var due_paid = parseFloat($('#due_paid').val());
                if (!due_paid) {
                    due_paid = 0;
                }
                var due = parseFloat($('#due').val());
                if (!due) {
                    due = 0;
                }

                if ((cash + due_paid + due) != total) {
                    e.preventDefault();
                    alert('Summation of cash, due paid and due should be equal to total amount.');
                }
            } else {
                var total = parseFloat($('#total').val());
                var new_total = parseFloat($('#new_total').val());
                var cash = parseFloat($('#cash').val());
                if (!cash) {
                    cash = 0;
                }
                var due_paid = parseFloat($('#due_paid').val());
                if (!due_paid) {
                    due_paid = 0;
                }
                var due = parseFloat($('#due').val());
                if (!due) {
                    due = 0;
                }

                if ((cash + due_paid + due) > total) {
                    e.preventDefault();
                    alert('Summation of cash, due paid and due exceed\'s total amount.');
                }

                if ((total - cash - due_paid - due) < new_total) {
                    e.preventDefault();
                    alert('Replacement total should be equal or less than Defect total amount.');
                }
            }
        });


    });

    $(document).on('click', '#is_replacement', function () {
        $('.warp_new_product').toggle()
    });

    $(document).on('change', '#customer_id', function () {
        var customer_id = $(this).val();
        var customer_type = $('#sales_customer_type option:selected').val();

        $.ajax({
            type: 'POST',
            url: '{{ route('ajax.get_person_due_amount') }}',
            data: {person_id: customer_id, person_type: customer_type, '_token': $('input[name=_token]').val()},
            success: function (data, status) {
                var html = '<div class="alert alert-success">' +
                        'This customer has ' + data + ' due.' +
                        '</div>';
                $('.pay_due').html(html);
                $('#due_paid').attr('max', data);

            },
            error: function (xhr, desc, err) {

            }
        })
    });

    function rowTotal(ele) {
        var q = $(ele).closest('.single_product').find('.single_p_quantity').val();
        var r = $(ele).closest('.single_product').find('.single_p_rate').val();
        if (q && r && (q * r) > 0) {
            $(ele).closest('.single_product').find('.single_p_total').val((parseFloat(q) * parseFloat(r)).toFixed(2));
            findTotal();
        }

    }

    function rowTotal2(ele) {
        var q = $(ele).closest('.single_product').find('.single_new_product_quantity').val();
        var r = $(ele).closest('.single_product').find('.single_new_product_rate').val();
        console.log(q);
        console.log(r);
        if (q && r && (q * r) > 0) {
            $(ele).closest('.single_product').find('.single_new_product_total').val((parseFloat(q) * parseFloat(r)).toFixed(2));
            findTotal2();
        }

    }
    function findTotal() {
        var total = 0;
        $.each($('.single_p_total'), function () {
            if (this.value)
                total += parseFloat(this.value);
        });
        $('#total').val(total.toFixed(2));
    }

    function findTotal2() {
        var total = 0;
        $.each($('.single_new_product_total'), function () {
            if (this.value)
                total += parseFloat(this.value);
        });
        $('#new_total').val(total.toFixed(2));
    }


</script>