{!! csrf_field() !!}

<div class="customer_type">
    <div class="form-group">
        {{ Form::label('customer_type', 'Customer Type', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7{{ $errors->has('customer_type') ? ' has-error' : '' }}">
            {{ Form::select('customer_type', Config::get('common.sales_customer_type'),3,['class'=>'form-control','id'=>'sales_customer_type','placeholder'=>'Select','disabled']) }}
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
            {{ Form::select('customer_id', $customers, $defect->customer_id,['class'=>'form-control','disabled']) }}
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
    @foreach($defect->defectItems as $defectItem)
        <div class="form-group single_product">
            <div class="col-md-offset-1 col-md-2">
                <input type="text" disabled="" value="{{ $defectItem->product['title'] }}"
                       class="form-control">
                <input type="hidden" name="old_product[0][product_id]" value="{{ $defectItem->product_id }}"
                       class="product_id">
            </div>
            <div class="col-md-3">
                <div style="width: 75%;float: left">
                    <input type="number" placeholder="Receive Quantity" name="old_product[0][receive_quantity]"
                           class="form-control pcal single_p_quantity" step="0.01" min="1" required=""
                           value="{{ $defectItem->quantity }}">
                </div>
                <div style="width: 25%;float: left">
                    <select style="padding:5px" class="form-control unit_type" name="old_product[0][unit_type]">
                        <option {{ $defectItem->unit_type==1? "selected=selected": "" }} value="1">ft</option>
                        <option {{ $defectItem->unit_type==2? "selected=selected": "" }} value="2">kg</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <input type="number" placeholder="Unit Price" name="old_product[0][unit_price]"
                       class="form-control pcal single_p_rate" step="0.01" min="0.01"
                       title="W: {{ $defectItem->product->wholesale_price }}, R: {{ $defectItem->product->retail_price }}"
                       data-weight="{{ $defectItem->product->weight }}" data-length="{{ $defectItem->product->length }}"
                       data-retail_price="{{ $defectItem->product->retail_price }}"
                       data-wholesale_price="{{ $defectItem->product->wholesale_price }}"
                       required="" value="{{  $defectItem->unit_price }}">
                <input type="hidden" name="old_product[0][length]" value="{{ $defectItem->product->length }}">
                <input type="hidden" name="old_product[0][weight]" value="{{ $defectItem->product->weight }}">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control single_p_total" readonly=""
                       value="{{ $defectItem->quantity*$defectItem->unit_price }}">
            </div>
            <div class="col-md-1">
                <span class="btn btn-danger remove_product">X</span>
            </div>
        </div>
    @endforeach
</div>

<div class="delete_product">

</div>

<div class="form-group">
    {{ Form::label('total', 'Total Amount', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('total') ? ' has-error' : '' }}">
        {{ Form::text('total', $defect->total,['class'=>'form-control','readonly']) }}
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
        {{ Form::number('cash', $defect->cash?  $defect->cash: '',['class'=>'form-control','min'=>0,'step'=>0.01]) }}
        @if ($errors->has('cash'))
            <span class="help-block">
                <strong>{{ $errors->first('cash') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="pay_due col-md-offset-3 col-md-7">
    <div class="well alert-info">
        This customer has {{ $personalAccount->due }} due.
    </div>
</div>

<div class="form-group">
    {{ Form::label('due_paid', 'Due Pay', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('due_paid') ? ' has-error' : '' }}">
        {{ Form::number('due_paid',  $defect->due_paid ? $defect->due_paid : '' ,['class'=>'form-control','min'=>0,'step'=>0.01]) }}
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
        {{ Form::number('due',  $defect->due ?  $defect->due : '',['class'=>'form-control','min'=>0,'step'=>0.01]) }}
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
        {{ Form::checkbox('is_replacement',1, $defect->is_replacement ? true : false,['form-control', 'onClick'=> $defect->is_replacement ? 'return false': '' ]) }}
        @if ($errors->has('is_replacement'))
            <span class="help-block">
                <strong>{{ $errors->first('is_replacement') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="warp_new_product" style="display: {{ $defect->is_replacement ? 'block' : 'none' }}">
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
        @if($defect->is_replacement)
            @foreach($salesOrder->salesOrderItems as $key=>$item)
                @if($item->status==4)
                    <div class='form-group single_product'>
                        <div class='col-md-2'>
                            <input type='text' class='form-control' value='{{ $item->product->title }}' disabled>

                        </div>
                        <div class='col-md-2'>
                            <input type='text' class='form-control stock_quantity'
                                   value='{{ App\Helpers\CommonHelper::get_current_stock($item->product_id).' (ft) / '.(App\Helpers\CommonHelper::get_current_stock($item->product_id)/$item->product->length)*$item->product->weight.' (kg)' }}'
                                   disabled>

                        </div>
                        <div class='col-md-3'>
                            <div style='width: 75%;float: left'>
                                <input disabled type='number' class='form-control new_pcal single_new_product_quantity'
                                       min="0.01" max="<?php if ($item->sales_unit_type == 1) {
                                    echo App\Helpers\CommonHelper::get_current_stock($item->product_id);
                                } else {
                                    echo (App\Helpers\CommonHelper::get_current_stock($item->product_id) / $item->product->length) * $item->product->weight;
                                } ?>" step="0.01"
                                       name='old_replacement_product[{{ $key }}][sales_quantity]'
                                       value="{{ $item->sales_quantity }}"
                                       placeholder='Sales Quantity'>
                            </div>
                            <div style='width: 25%;float: left'>
                                <select disabled name='old_replacement_product[{{ $key }}][sales_unit_type]'
                                        class='form-control sales_unit_type'
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
                                   type='number' min="0.01" step="0.01"
                                   class='form-control new_pcal single_new_product_rate'
                                   value="{{ $item->unit_price }}"
                                   name='old_replacement_product[{{ $key }}][unit_price]' placeholder='Unit Price'>
                        </div>
                        <div class='col-md-2'>
                            <input type='text' class='form-control single_new_product_total' disabled
                                   value="{{ $item->unit_price*$item->sales_quantity }}">
                        </div>
                    </div>
                @elseif($item->status==2)
                    <div class='form-group single_product'>
                        <div class='col-md-2'>
                            <input type='text' class='form-control' value='{{ $item->product->title }}' disabled>
                            <input class='product_id' type='hidden' value='{{ $item->product_id }}'
                                   name='old_replacement_product[{{ $key }}][product_id]'>
                        </div>
                        <div class='col-md-2'>
                            <input type='text' class='form-control stock_quantity'
                                   value='{{ App\Helpers\CommonHelper::get_current_stock($item->product_id).' (ft) / '.(App\Helpers\CommonHelper::get_current_stock($item->product_id)/$item->product->length)*$item->product->weight.' (kg)'  }}'
                                   disabled>

                        </div>
                        <div class='col-md-3'>
                            <div style='width: 75%;float: left'>
                                <input required
                                       data-delivered="{{ App\Helpers\CommonHelper::get_delivered_quantity($salesOrder->id,$item->product_id) }}"
                                       title="Delivered Quantity: {{ App\Helpers\CommonHelper::get_delivered_quantity($salesOrder->id,$item->product_id) }}"
                                       type='number' class='form-control new_pcal single_new_product_quantity'
                                       min="{{ isset($item->salesDelivery->delivered_quantity)? $item->salesDelivery->delivered_quantity : 1 }}"
                                       max="<?php if ($item->sales_unit_type == 1) {
                                           echo App\Helpers\CommonHelper::get_current_stock($item->product_id);
                                       } else {
                                           echo (App\Helpers\CommonHelper::get_current_stock($item->product_id) / $item->product->length) * $item->product->weight;
                                       } ?>" step="0.01"
                                       name='old_replacement_product[{{ $key }}][sales_quantity]'
                                       value="{{ $item->sales_quantity }}"
                                       placeholder='Sales Quantity'>
                            </div>
                            <div style='width: 25%;float: left'>
                                <select {{ isset($item->salesDelivery->delivered_quantity)? 'disabled' : '' }} name='old_replacement_product[{{ $key }}][sales_unit_type]'
                                        class='form-control sales_unit_type'
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

                                @if(isset($item->salesDelivery->delivered_quantity))
                                    <input type="hidden" name="old_replacement_product[{{ $key }}][sales_unit_type]"
                                           value="{{ $item->sales_unit_type }}">
                                @endif
                            </div>
                        </div>
                        <div class='col-md-2'>
                            <input required
                                   data-wholesale_price="{{ $item->product->wholesale_price }}"
                                   data-retail_price="{{ $item->product->retail_price }}"
                                   data-length="{{$item->product->length}}" data-weight="{{ $item->product->weight }}"
                                   title='W: "{{ round($item->product->wholesale_price/$item->product->length,2) }}", R: "{{ round($item->product->retail_price/$item->product->length,2) }}"'
                                   type='number' min="0.01" step="0.01"
                                   class='form-control new_pcal single_new_product_rate'
                                   value="{{ $item->unit_price }}"
                                   name='old_replacement_product[{{ $key }}][unit_price]' placeholder='Unit Price'>
                        </div>
                        <div class='col-md-2'>
                            <input type='text' class='form-control single_new_product_total'
                                   value="{{ $item->unit_price*$item->sales_quantity }}">
                        </div>
                        @if(!isset($item->salesDelivery->id))
                            <div class='col-md-1'>
                                <span class='btn btn-danger remove_new_product'>X</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class='form-group single_product'>
                        <div class='col-md-2'>
                            <input type='text' class='form-control' value='{{ $item->product->title }}' disabled>
                            <input class='product_id' type='hidden' value='{{ $item->product_id }}'
                                   name='old_replacement_product[{{ $key }}][product_id]'>
                        </div>
                        <div class='col-md-2'>
                            <input type='text' class='form-control stock_quantity'
                                   value='{{ App\Helpers\CommonHelper::get_current_stock($item->product_id).' (ft) / '.(App\Helpers\CommonHelper::get_current_stock($item->product_id)/$item->product->length)*$item->product->weight.' (kg)'  }}'
                                   disabled>

                        </div>
                        <div class='col-md-3'>
                            <div style='width: 75%;float: left'>
                                <input required type='number' min="0.01" max="<?php if ($item->sales_unit_type == 1) {
                                    echo App\Helpers\CommonHelper::get_current_stock($item->product_id);
                                } else {
                                    echo (App\Helpers\CommonHelper::get_current_stock($item->product_id) / $item->product->length) * $item->product->weight;
                                } ?>" step="0.01" class='form-control new_pcal single_new_product_quantity'
                                       name='old_replacement_product[{{ $key }}][sales_quantity]'
                                       value="{{ $item->sales_quantity }}"
                                       placeholder='Sales Quantity'>
                            </div>
                            <div style='width: 25%;float: left'>
                                <select name='old_replacement_product[{{ $key }}][sales_unit_type]'
                                        class='form-control sales_unit_type'
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
                                   type='number' min="0.01" step="0.01"
                                   class='form-control new_pcal single_new_product_rate'
                                   value="{{ $item->unit_price }}"
                                   name='old_replacement_product[{{ $key }}][unit_price]' placeholder='Unit Price'>
                            <input type="hidden" name="old_replacement_product[{{ $key }}][length]"
                                   value="{{ $item->product->length }}">
                            <input type="hidden" name="old_replacement_product[{{ $key }}][weight]"
                                   value="{{ $item->product->weight }}">
                        </div>
                        <div class='col-md-2'>
                            <input type='text' class='form-control single_new_product_total'
                                   value="{{ $item->unit_price*$item->sales_quantity }}">
                        </div>
                        <div class='col-md-1'>
                            <span class='btn btn-danger remove_new_product'>X</span>
                        </div>
                    </div>
                @endif

            @endforeach
        @endif

    </div>


    <div class="delete_new_product">

    </div>


    <div class="form-group">
        {{ Form::label('new_total', 'Total Amount', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7{{ $errors->has('new_total') ? ' has-error' : '' }}">
            {{ Form::text('new_total', $salesOrder->total,['class'=>'form-control','readonly']) }}
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
                        "<input  class='form-control stock_quantity' disabled value='" + quantity + " (ft) / " + ((quantity / ui.item.length) * ui.item.weight).toFixed(1) + " (kg)' data-stock-ft='" + quantity + "' data-stock-kg='" + ((quantity / ui.item.length) * ui.item.weight).toFixed(1) + "'> " +
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
                        "<input  value='" + ui.item.length + "' type='hidden' name='new_product[" + index + "][length]' > " +
                        "<input  value='" + ui.item.weight + "' type='hidden' name='new_product[" + index + "][weight]' > " +
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
            var product_id = obj.closest('.single_product').find('.product_id').val();
            var html = '<input type="hidden" name="delete_product[][product_id]"  value="' + product_id + '">'
            $('.delete_product').append(html);
            obj.closest('.single_product').remove();
            findTotal();
        });

        $(document).on('click', '.remove_new_product', function () {
            var obj = $(this);
            var product_id = obj.closest('.single_product').find('.product_id').val();
            var html = '<input type="hidden" name="delete_replacement_product[][product_id]"  value="' + product_id + '">'
            $('.delete_new_product').append(html);
            obj.closest('.single_product').remove();
            findTotal2();
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
                var quantity = parseFloat(obj.closest('.single_product').find('.stock_quantity').data('stock-kg'));
                obj.closest('.single_product').find('.single_new_product_quantity').attr('max', quantity);
            } else {
                var length = parseFloat(obj.closest('.single_product').find('.single_new_product_rate').data('length'));
                var title = 'W: ' + (wholesale_price / length).toFixed(2) + ', R: ' + (retail_price / length).toFixed(2);
                obj.closest('.single_product').find('.single_new_product_rate').attr('title', title);
                var quantity = parseFloat(obj.closest('.single_product').find('.stock_quantity').data('stock-ft'));
                obj.closest('.single_product').find('.single_new_product_quantity').attr('max', quantity);
            }
        });

        $(document).on('submit', 'form', function (e) {
            var due = parseFloat($('#due').val());
            if (due > 0) {
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
            }
        });

        $(document).on('click', '#is_replacement', function () {
            $('.warp_new_product').toggle()
        });


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