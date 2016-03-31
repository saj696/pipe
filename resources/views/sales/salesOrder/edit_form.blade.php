{!! csrf_field() !!}

<div class="form-group">
    {{ Form::label('customer_type', 'Customer Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('customer_type') ? ' has-error' : '' }}">
        {{ Form::select('customer_type', Config::get('common.sales_customer_type'), $salesOrder->customer_type,['class'=>'form-control','id'=>'sales_customer_type','disabled','placeholder'=>'Select']) }}
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
            {{ Form::select('customer_id', $customers, $salesOrder->customer_id,['class'=>'form-control','placeholder'=>'Select','disabled']) }}
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
    @foreach($salesOrder->salesOrderItems as $key=>$item)
        @if($item->status==4)
            <div class='form-group single_product'>
                <div class='col-md-2'>
                    <input type='text' class='form-control' value='{{ $item->product->title }}' disabled>

                </div>
                <div class='col-md-2'>
                    <input type='text' class='form-control'
                           value='{{ App\Helpers\CommonHelper::get_current_stock($item->product_id).' (ft) / '.(App\Helpers\CommonHelper::get_current_stock($item->product_id)/$item->product->length)*$item->product->weight.' (kg)' }}'
                           disabled>

                </div>
                <div class='col-md-3'>
                    <div style='width: 75%;float: left'>
                        <input disabled type='number' class='form-control pcal single_p_quantity'
                               min="0.01" max="<?php if ($item->sales_unit_type == 1) {
                            echo (App\Helpers\CommonHelper::get_current_stock($item->product_id) / $item->product->weight) * $item->product->length;
                        } else {
                            echo (App\Helpers\CommonHelper::get_current_stock($item->product_id) / $item->product->length) * $item->product->weight;
                        } ?>" step="0.01"
                               name='product[{{ $key }}][sales_quantity]' value="{{ $item->sales_quantity }}"
                               placeholder='Sales Quantity'>
                    </div>
                    <div style='width: 25%;float: left'>
                        <select name='product[{{ $key }}][sales_unit_type]' class='form-control sales_unit_type'
                                style='padding:5px'>
                            <option <?php if ($item->sales_unit_type == 1) {
                                echo "selected";
                            } ?> value='1'>ft
                            </option>
                            <option <?php if ($item->sales_unit_type == 2) {
                                echo "selected";
                            } ?> value='2'>kg
                            </option>
                        </select>
                    </div>
                </div>
                <div class='col-md-2'>
                    <input disabled
                           data-wholesale_price="{{ $item->product->wholesale_price }}"
                           data-retail_price="{{ $item->product->retail_price }}"
                           data-length="{{$item->product->length}}" data-weight="{{ $item->product->weight }}"
                           title='W: "{{ round($item->product->wholesale_price/$item->product->length,2) }}", R: "{{ round($item->product->retail_price/$item->product->length,2) }}"'
                           type='number' min="0.01" step="0.01" class='form-control pcal single_p_rate'
                           value="{{ $item->unit_price }}"
                           name='product[{{ $key }}][unit_price]' placeholder='Unit Price'>
                </div>
                <div class='col-md-2'>
                    <input readonly type='text' class='form-control single_p_total' disabled
                           value="{{ $item->unit_price*$item->sales_quantity }}">
                </div>
            </div>
        @elseif($item->status==2)
            <div class='form-group single_product'>
                <div class='col-md-2'>
                    <input type='text' class='form-control' value='{{ $item->product->title }}' disabled>
                    <input class='product_id' type='hidden' value='{{ $item->product_id }}'
                           name='product[{{ $key }}][product_id]'>
                </div>
                <div class='col-md-2'>
                    <input type='text' class='form-control'
                           value='{{ App\Helpers\CommonHelper::get_current_stock($item->product_id).' (ft) / '.(App\Helpers\CommonHelper::get_current_stock($item->product_id)/$item->product->length)*$item->product->weight.' (kg)'  }}'
                           disabled>

                </div>
                <div class='col-md-3'>
                    <div style='width: 75%;float: left'>
                        <input required
                               data-delivered="{{ App\Helpers\CommonHelper::get_delivered_quantity($salesOrder->id,$item->product_id) }}"
                               title="Delivered Quantity: {{ App\Helpers\CommonHelper::get_delivered_quantity($salesOrder->id,$item->product_id) }}"
                               type='number' class='form-control pcal single_p_quantity'
                               min="0.01" max="<?php if ($item->sales_unit_type == 1) {
                            echo (App\Helpers\CommonHelper::get_current_stock($item->product_id) / $item->product->weight) * $item->product->length;
                        } else {
                            echo (App\Helpers\CommonHelper::get_current_stock($item->product_id) / $item->product->length) * $item->product->weight;
                        } ?>" step="0.01"
                               name='product[{{ $key }}][sales_quantity]' value="{{ $item->sales_quantity }}"
                               placeholder='Sales Quantity'>
                    </div>
                    <div style='width: 25%;float: left'>
                        <select name='product[{{ $key }}][sales_unit_type]' class='form-control sales_unit_type'
                                style='padding:5px'>
                            <option <?php if ($item->sales_unit_type == 1) {
                                echo "selected";
                            } ?> value='1'>ft
                            </option>
                            <option <?php if ($item->sales_unit_type == 2) {
                                echo "selected";
                            } ?> value='2'>kg
                            </option>
                        </select>
                    </div>
                </div>
                <div class='col-md-2'>
                    <input required
                           data-wholesale_price="{{ $item->product->wholesale_price }}"
                           data-retail_price="{{ $item->product->retail_price }}"
                           data-length="{{$item->product->length}}" data-weight="{{ $item->product->weight }}"
                           title='W: "{{ round($item->product->wholesale_price/$item->product->length,2) }}", R: "{{ round($item->product->retail_price/$item->product->length,2) }}"'
                           type='number' min="0.01" step="0.01" class='form-control pcal single_p_rate'
                           value="{{ $item->unit_price }}"
                           name='product[{{ $key }}][unit_price]' placeholder='Unit Price'>
                </div>
                <div class='col-md-2'>
                    <input readonly type='text' class='form-control single_p_total'
                           value="{{ $item->unit_price*$item->sales_quantity }}">
                </div>
                @if(!isset($item->salesDelivery->id))
                    <div class='col-md-1'>
                        <span class='btn btn-danger remove_product'>X</span>
                    </div>
                @endif

            </div>
        @else
            <div class='form-group single_product'>
                <div class='col-md-2'>
                    <input type='text' class='form-control' value='{{ $item->product->title }}' disabled>
                    <input class='product_id' type='hidden' value='{{ $item->product_id }}'
                           name='product[{{ $key }}][product_id]'>
                </div>
                <div class='col-md-2'>
                    <input type='text' class='form-control'
                           value='{{ App\Helpers\CommonHelper::get_current_stock($item->product_id).' (ft) / '.(App\Helpers\CommonHelper::get_current_stock($item->product_id)/$item->product->length)*$item->product->weight.' (kg)'  }}'
                           disabled>

                </div>
                <div class='col-md-3'>
                    <div style='width: 75%;float: left'>
                        <input required type='number' min="0.01" max="<?php if ($item->sales_unit_type == 1) {
                            echo (App\Helpers\CommonHelper::get_current_stock($item->product_id) / $item->product->weight) * $item->product->length;
                        } else {
                            echo (App\Helpers\CommonHelper::get_current_stock($item->product_id) / $item->product->length) * $item->product->weight;
                        } ?>" step="0.01" class='form-control pcal single_p_quantity'
                               name='product[{{ $key }}][sales_quantity]' value="{{ $item->sales_quantity }}"
                               placeholder='Sales Quantity'>
                    </div>
                    <div style='width: 25%;float: left'>
                        <select name='product[{{ $key }}][sales_unit_type]' class='form-control sales_unit_type'
                                style='padding:5px'>
                            <option <?php if ($item->sales_unit_type == 1) {
                                echo "selected";
                            } ?> value='1'>ft
                            </option>
                            <option <?php if ($item->sales_unit_type == 2) {
                                echo "selected";
                            } ?> value='2'>kg
                            </option>
                        </select>
                    </div>
                </div>
                <div class='col-md-2'>
                    <input required
                           data-wholesale_price="{{ $item->product->wholesale_price }}"
                           data-retail_price="{{ $item->product->retail_price }}"
                           data-length="{{$item->product->length}}" data-weight="{{ $item->product->weight }}"
                           title='W: "{{ round($item->product->wholesale_price/$item->product->length,2) }}", R: "{{ round($item->product->retail_price/$item->product->length,2) }}"'
                           type='number' min="0.01" step="0.01" class='form-control pcal single_p_rate'
                           value="{{ $item->unit_price }}"
                           name='product[{{ $key }}][unit_price]' placeholder='Unit Price'>
                </div>
                <div class='col-md-2'>
                    <input readonly type='text' class='form-control single_p_total'
                           value="{{ $item->unit_price*$item->sales_quantity }}">
                </div>
                <div class='col-md-1'>
                    <span class='btn btn-danger remove_product'>X</span>
                </div>
            </div>
        @endif

    @endforeach
</div>

<div class="form-group">
    {{ Form::label('total', 'Total Amount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('total') ? ' has-error' : '' }}">
        {{ Form::text('total', $salesOrder->total+$salesOrder->discount,['class'=>'form-control','readonly']) }}
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
        {{ Form::number('discount', null,['class'=>'form-control','min'=>0,'step'=>0.01]) }}
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
        {{ Form::number('transport_cost', null,['class'=>'form-control','min'=>0,'step'=>0.01]) }}
        @if ($errors->has('transport_cost'))
            <span class="help-block">
                <strong>{{ $errors->first('transport_cost') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('labour_cost', 'Labour Cost', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('labor_cost') ? ' has-error' : '' }}">
        {{ Form::number('labour_cost', null,['class'=>'form-control','min'=>0,'step'=>0.01]) }}
        @if ($errors->has('labour_cost'))
            <span class="help-block">
                <strong>{{ $errors->first('labour_cost') }}</strong>
            </span>
        @endif
    </div>
</div>


<div class="form-group">
    {{ Form::label('paid', 'Paid Amount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('paid') ? ' has-error' : '' }}">
        {{ Form::number('paid', null,['class'=>'form-control','min'=>0,'step'=>0.01]) }}
        @if ($errors->has('paid'))
            <span class="help-block">
                <strong>{{ $errors->first('paid') }}</strong>
            </span>
        @endif
    </div>
</div>

@if($salesOrder->customer_id)
    <div class="form-group">
        {{ Form::label('personal_account', 'Personal Account Balance', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7">
            {{ Form::number('personal_account', $personalAccount->balance,['class'=>'form-control','disabled','id'=>'personal_account_balance']) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('paid_from_personal_account', 'Paid From Personal Account', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7">
            {{ Form::number('paid_from_personal_account', $salesOrder->personal_account_paid,['class'=>'form-control','min'=>0,'step'=>0.01,'max'=>$personalAccount->balance+$salesOrder->personal_account_paid]) }}
        </div>
    </div>
@endif

<div class="form-group">
    {{ Form::label('due', 'Due', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('due') ? ' has-error' : '' }}">
        {{ Form::number('due', null,['class'=>'form-control','min'=>0,'step'=>0.01,'readonly']) }}
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
        {{ Form::textarea('remarks', null,['class'=>'form-control', 'rows'=>'3']) }}
        @if ($errors->has('remarks'))
            <span class="help-block">
                <strong>{{ $errors->first('remarks') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="delete_product">

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
                var quantity = 0;
                if (ui.item.quantity > 0) {
                    quantity = ui.item.quantity;
                }
                var index = $('.product_list').data('product-id');
                var html = "<div class='form-group single_product'>" +
                        "<div class='col-md-2'>" +
                        " <input type='text' class='form-control' value='" + ui.item.label + "' disabled>" +
                        "<input class='product_id' type='hidden' value='" + ui.item.value + "' name='product[" + index + "][product_id]'> " +
                        "</div>" +
                        "<div class='col-md-2'>" +
                        "<input  class='form-control' disabled value='" + quantity + " (ft) / "+ ((quantity/ui.item.length)*ui.item.weight).toFixed(1) +" (kg)'> " +
                        "</div>" +
                        "<div class='col-md-3'>" +
                        "<div style='width: 75%;float: left'>" +
                        "<input required type='number' min='1' step='0.01' max='" + ui.item.quantity + "' class='form-control pcal single_p_quantity' name='product[" + index + "][sales_quantity]' placeholder='Sales Quantity'> " +
                        "</div>" +
                        "<div style='width: 25%;float: left'>" +
                        "<select name='product[" + index + "][sales_unit_type]' class='form-control sales_unit_type' style='padding:5px'> " +
                        "<option value='1'>ft</option>" +
                        "<option value='2'>kg</option>" +
                        "</select>" +
                        "</div>" +
                        "</div>" +
                        "<div class='col-md-2'>" +
                        "<input required data-wholesale_price='" + ui.item.wholesale_price + "' data-retail_price='" + ui.item.retail_price + "' data-length='" + ui.item.length + "' data-weight='" + ui.item.weight + "' title='W: " + (ui.item.wholesale_price / ui.item.length).toFixed(2) + ", R: " + (ui.item.retail_price / ui.item.length).toFixed(2) + "' type='number' min='0.01' step='0.01' class='form-control pcal single_p_rate' name='product[" + index + "][unit_price]' placeholder='Unit Price'> " +
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
            var product_id = obj.closest('.single_product').find('.product_id').val()
            var html = '<input type="hidden" name="delete_product[][product_id]" value="' + product_id + '">'
            $('.delete_product').append(html);
            obj.closest('.single_product').remove();
            findTotal();
            calculateAmount();
        });

        $(document).on('change', '.pcal', function () {
            var delivered = parseFloat($(this).data('delivered'));
            var quantity = parseFloat($(this).val());
            if (quantity < delivered) {
                alert('Minimum Quantity: ' + delivered);
                $(this).val("");
            }
            rowTotal(this);
        });

        $(document).on('change', '#paid', function () {
            var paid = parseFloat($(this).val());
            var discount = parseFloat($('#discount').val());
            var t_cost = parseFloat($('#transport_cost').val());
            var total_amount = parseFloat($('#total').val());

            if (paid > (total_amount + t_cost) - discount) {
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

        $(document).on('change', '#paid_from_personal_account', function () {
            calculateAmount()
        });

        $(document).on('change', '.sales_unit_type', function () {
            var type = parseInt($(this).val());
            var obj = $(this);
            var wholesale_price = parseFloat(obj.closest('.single_product').find('.single_p_rate').data('wholesale_price'));
            var retail_price = parseFloat(obj.closest('.single_product').find('.single_p_rate').data('retail_price'));
            if (type == 2) {
                var weight = parseFloat(obj.closest('.single_product').find('.single_p_rate').data('weight'));
                var title = 'W: ' + (wholesale_price / weight).toFixed(2) + ', R: ' + (retail_price / weight).toFixed(2);
                obj.closest('.single_product').find('.single_p_rate').attr('title', title);

                var quantity = parseFloat(obj.closest('.single_product').find('.single_p_quantity').attr('max'));
                var length = parseFloat(obj.closest('.single_product').find('.single_p_rate').data('length'));
                var max = (quantity / length) * weight;
                obj.closest('.single_product').find('.single_p_quantity').attr('max', max.toFixed(2));
            } else {
                var length = parseFloat(obj.closest('.single_product').find('.single_p_rate').data('length'));
                var title = 'W: ' + (wholesale_price / length).toFixed(2) + ', R: ' + (retail_price / length).toFixed(2);
                obj.closest('.single_product').find('.single_p_rate').attr('title', title);

                var quantity = parseFloat(obj.closest('.single_product').find('.single_p_quantity').attr('max'));
                var weight = parseFloat(obj.closest('.single_product').find('.single_p_rate').data('weight'));
                var max = (quantity / weight) * length;
                obj.closest('.single_product').find('.single_p_quantity').attr('max', max.toFixed(2));
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
        calculateAmount();
    }

    function calculateAmount() {
        var discount = parseFloat($('#discount').val());
        var t_cost = parseFloat($('#transport_cost').val());
        var labour_cost = parseFloat($('#labour_cost').val());
        var paid_amount = parseFloat($('#paid').val());
        var paid_amount_from_personal_account = parseFloat($('#paid_from_personal_account').val());
        var total_amount = parseFloat($('#total').val());
        var due = 0;
        if (!discount) {
            discount = 0;
        }
        if (!t_cost) {
            t_cost = 0;
        }
        if (!labour_cost) {
            labour_cost = 0;
        }
        if (!paid_amount) {
            paid_amount = 0
        }
        if (!paid_amount_from_personal_account) {
            paid_amount_from_personal_account = 0
        }
        due = (total_amount + t_cost + labour_cost) - (paid_amount + discount + paid_amount_from_personal_account);
        if (due > 0 || due < 0) {
            $('#due').val(due.toFixed(2));
        } else {
            $('#due').val('');
        }

    }
</script>