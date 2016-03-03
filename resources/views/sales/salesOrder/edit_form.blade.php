{!! csrf_field() !!}

<div class="form-group{{ $errors->has('customer_type') ? ' has-error' : '' }}">
    {{ Form::label('customer_type', 'Customer Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('customer_type', Config::get('common.sales_customer_type'), Config::get('common.person_type_customer'),['class'=>'form-control','id'=>'sales_customer_type','disabled']) }}
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
            {{ Form::select('customer_id', $customers, null,['class'=>'form-control','placeholder'=>'Select','disabled']) }}
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

<div class="product_list" data-product-id="{{sizeof($salesOrder->salesOrderItems)}}">
    <div class='col-md-offset-3 col-md-2'>
        <label for="">Product Name</label>
    </div>
    <div class='col-md-2'>
        <label for="">Quantity</label>
    </div>
    <div class='col-md-2'>
        <label for="">Unit Price</label>
    </div>
    <div class='col-md-2'>
        <label for="">Total</label>
    </div>
    @foreach($salesOrder->salesOrderItems as $key=>$item)
        @if($item->status==4)
            <div class='form-group single_product'>
                <div class='col-md-offset-3 col-md-2'>
                    <input type='text' class='form-control' value='{{ $item->product->title }}' disabled>

                </div>
                <div class='col-md-2'>
                    <input disabled type='text' class='form-control pcal single_p_quantity'
                           name='product[{{ $key }}][sales_quantity]' value="{{ $item->sales_quantity }}"
                           placeholder='Sales Quantity'>
                </div>
                <div class='col-md-2'>
                    <input disabled
                           title='W: "{{ $item->product->wholesale_price }}", R: "{{ $item->product->retail_price }}"'
                           type='text' class='form-control pcal single_p_rate' value="{{ $item->unit_price }}"
                           name='product[{{ $key }}][unit_price]' placeholder='Unit Price'>
                </div>
                <div class='col-md-2'>
                    <input type='text' class='form-control single_p_total' disabled
                           value="{{ $item->unit_price*$item->sales_quantity }}">
                </div>
            </div>
        @elseif($item->status==2)
            <div class='form-group single_product'>
                <div class='col-md-offset-3 col-md-2'>
                    <input type='text' class='form-control' value='{{ $item->product->title }}' disabled>
                    <input class='product_id' type='hidden' value='{{ $item->product_id }}'
                           name='product[{{ $key }}][product_id]'>
                </div>
                <div class='col-md-2'>
                    <input required
                           data-delivered="{{ App\Helpers\CommonHelper::get_delivered_quantity($salesOrder->id,$item->product_id) }}"
                           title="Delivered Quantity: {{ App\Helpers\CommonHelper::get_delivered_quantity($salesOrder->id,$item->product_id) }}"
                           type='text' class='form-control pcal single_p_quantity'
                           name='product[{{ $key }}][sales_quantity]' value="{{ $item->sales_quantity }}"
                           placeholder='Sales Quantity'>
                </div>
                <div class='col-md-2'>
                    <input required
                           title='W: "{{ $item->product->wholesale_price }}", R: "{{ $item->product->retail_price }}"'
                           type='text' class='form-control pcal single_p_rate' value="{{ $item->unit_price }}"
                           name='product[{{ $key }}][unit_price]' placeholder='Unit Price'>
                </div>
                <div class='col-md-2'>
                    <input type='text' class='form-control single_p_total'
                           value="{{ $item->unit_price*$item->sales_quantity }}">
                </div>
            </div>
        @else
            <div class='form-group single_product'>
                <div class='col-md-offset-3 col-md-2'>
                    <input type='text' class='form-control' value='{{ $item->product->title }}' disabled>
                    <input class='product_id' type='hidden' value='{{ $item->product_id }}'
                           name='product[{{ $key }}][product_id]'>
                </div>
                <div class='col-md-2'>
                    <input required type='text' class='form-control pcal single_p_quantity'
                           name='product[{{ $key }}][sales_quantity]' value="{{ $item->sales_quantity }}"
                           placeholder='Sales Quantity'>
                </div>
                <div class='col-md-2'>
                    <input required
                           title='W: "{{ $item->product->wholesale_price }}", R: "{{ $item->product->retail_price }}"'
                           type='text' class='form-control pcal single_p_rate' value="{{ $item->unit_price }}"
                           name='product[{{ $key }}][unit_price]' placeholder='Unit Price'>
                </div>
                <div class='col-md-2'>
                    <input type='text' class='form-control single_p_total'
                           value="{{ $item->unit_price*$item->sales_quantity }}">
                </div>
                <div class='col-md-1'>
                    <span class='btn btn-danger remove_product'>X</span>
                </div>
            </div>
        @endif

    @endforeach
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
                var index = $('.product_list').data('product-id');
                var html = "<div class='form-group single_product'>" +
                        "<div class='col-md-offset-3 col-md-2'>" +
                        " <input type='text' class='form-control' value='" + ui.item.label + "' disabled>" +
                        "<input class='product_id' type='hidden' value='" + ui.item.value + "' name='product[" + index + "][product_id]'> " +
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
            var delivered= parseFloat($(this).data('delivered'));
            var quantity= parseFloat($(this).val());
            if(quantity < delivered)
            {
                alert('Minimum Quantity: '+ delivered);
                $(this).val("");
            }
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