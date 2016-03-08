{!! csrf_field() !!}
<div class="form-group">
    {{ Form::label('supplier_id', 'Supplier', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('supplier_id') ? ' has-error' : '' }}">
        {{ Form::select('supplier_id',$suppliers, null,['class'=>'form-control']) }}
        @if ($errors->has('supplier_id'))
            <span class="help-block">
                <strong>{{ $errors->first('supplier_id') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('purchase_date', 'Purchase Date', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('purchase_date') ? ' has-error' : '' }}">
        {{ Form::text('purchase_date', null,['class'=>'form-control datepicker','required']) }}
        @if ($errors->has('purchase_date'))
            <span class="help-block">
                <strong>{{ $errors->first('purchase_date') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group" style="margin-bottom: 40px">
    {{ Form::label('transportation_cost', 'Transportation Cost', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('transportation_cost') ? ' has-error' : '' }}">
        {{ Form::text('transportation_cost', 0,['class'=>'form-control']) }}
        @if ($errors->has('transportation_cost'))
            <span class="help-block">
                <strong>{{ $errors->first('transportation_cost') }}</strong>
            </span>
        @endif
    </div>
</div>
<?php
$old_items = isset($purchase) ? $purchase['purchaseDetails'] : false;
?>
<div class="row" id="purchase_wrp"
    data-current-index="{{ isset($purchase) ?  count($purchase['purchaseDetails']) : 0}}">
@if($old_items)
    @foreach($old_items as $item)
        <div class="col-md-12 purchase_row">
            <table class="table table-bordered">
                <tr>
                    <td width="25%">
                        {{ Form::select('items['.$item['id'].'][material_id]',$materials, $item['material_id'],['class'=>'form-control material_id','id'=>'','required','placeholder'=>'Select Material']) }}
                    </td>
                    <td>
                        {{ Form::number('items['.$item['id'].'][quantity]', $item['quantity'],['class'=>'form-control quantity','id'=>'','required','placeholder'=>'Quantity','min'=>1,'step'=>0.01]) }}
                    </td>
                    <td>
                        {{ Form::number('items['.$item['id'].'][received_quantity]', $item['received_quantity'],['class'=>'form-control received_quantity','required','id'=>'','placeholder'=>'Received Quantity','min'=>0,'step'=>0.01]) }}

                    </td>
                    <td>
                        {{ Form::number('items['.$item['id'].'][unit_price]', $item['unit_price'],['class'=>'form-control unit_price','id'=>'','required','placeholder'=>'Unit Price','min'=>0.01,'step'=>0.01]) }}
                    </td>
                    <td width="15%" class="text-center">
                        <span class="badge badge-info row_total"
                              style="margin-top: 10px">{{$item['unit_price']*$item['quantity']}}</span>
                    </td>
                    <td>
                        <i class="fa fa-close" onclick="closeIt(this)"
                           style="color: red;margin-top: 10px; cursor: pointer"></i>
                    </td>
                </tr>
            </table>
            </div>
            @endforeach
            @else
                <div class="col-md-offset-1 col-md-10 purchase_row">
                    <table class="table table-bordered">
                        <tr>
                            <td width="25%">
                                {{ Form::select('items[0][material_id]',$materials, null,['class'=>'form-control material_id','id'=>'','required','placeholder'=>'Select Material']) }}
                            </td>
                            <td>
                                {{ Form::number('items[0][quantity]', null,['class'=>'form-control quantity','id'=>'','required','placeholder'=>'Quantity','min'=>1,'step'=>0.01]) }}
                            </td>
                            <td>
                                {{ Form::number('items[0][received_quantity]', null,['class'=>'form-control received_quantity','required','id'=>'','placeholder'=>'Received Quantity','min'=>0,'step'=>0.01]) }}
                            </td>
                            <td>
                                {{ Form::number('items[0][unit_price]', null,['class'=>'form-control unit_price','id'=>'','required','placeholder'=>'Unit Price','min'=>0.01,'step'=>0.01]) }}
                            </td>
                            <td width="15%" class="text-center">
                                <span class="badge badge-info row_total" style="margin-top: 10px">0</span>
                            </td>
                            <td>
                                <i class="fa fa-close" onclick="closeIt(this)"
                                   style="color: red;margin-top: 10px; cursor: pointer"></i>
                            </td>
                        </tr>
                    </table>
                </div>
                @endif

                </div>
                <div class="col-md-12 text-center">
                    <button type="button" onclick="addMore()" class="btn-circle btn btn-success" style="margin: 10px 0">
                        Add
                        more
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-3 col-md-offset-9">
                        <div class="form-group">
                            <label class="col-md-5 control-label" for="paid">Total Amount</label>
                            <div class="col-md-7">
                                {{ Form::text('total', null,['class'=>'form-control unit_price','readonly'=>'readonly','id'=>'total_amount','required']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-md-offset-9">
                        <div class="form-group{{ $errors->has('paid') ? ' has-error' : '' }}">
                            {{ Form::label('paid', 'Paid Amount', ['class'=>'col-md-5 control-label']) }}
                            <div class="col-md-7">
                                {{ Form::number('paid', null,['class'=>'form-control','min'=>0,'step'=>0.01]) }}
                                @if ($errors->has('paid'))
                                    <span class="help-block">
                <strong>{{ $errors->first('paid') }}</strong>
            </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="text-center col-md-12">
                            {{ Form::submit($submitText, ['class'=>'btn btn-circle green']) }}
                        </div>
                    </div>
                </div>
                <script type="text/javascript">
                    $(document).on('ready', function () {
                        findTotal();
                    });
                    $(function () {
                        $(".datepicker").datepicker();
                    });
                    function addMore() {
                        var currentIndex = parseInt($('#purchase_wrp').data('current-index'));
                        var newIndex = currentIndex + 1;
                        parseInt($('#purchase_wrp').data('current-index', newIndex));

                        var rowHtml = $('#purchase_wrp :first').clone();
                        $('#purchase_wrp').append(rowHtml);
                        $('#purchase_wrp:last').find('.material_id:last').attr('name', 'items[' + newIndex + '][material_id]');
                        $('#purchase_wrp:last').find('.quantity:last').attr('name', 'items[' + newIndex + '][quantity]');
                        $('#purchase_wrp:last').find('.received_quantity:last').attr('name', 'items[' + newIndex + '][received_quantity]');
                        $('#purchase_wrp:last').find('.unit_price:last').attr('name', 'items[' + newIndex + '][unit_price]');
                        $('#purchase_wrp:last').find('.material_id:last').val('');
                        $('#purchase_wrp:last').find('.quantity:last').val('');
                        $('#purchase_wrp:last').find('.received_quantity:last').val('');
                        $('#purchase_wrp:last').find('.unit_price:last').val('');
                        $('#purchase_wrp:last').find('.row_total:last').html('');
                    }
                    function closeIt(ele) {
                        var noOfChild = $('#purchase_wrp .purchase_row').length;
                        if (noOfChild < 2) {
                            alert('You Can\'t remove all items');
                            return false;
                        }
                        ele.closest('.purchase_row').remove();
                        findTotal();
                    }
                    $(document).on('change', '.quantity', function () {
                        findRowTotal(this);
                        findTotal();
                    });

                    $(document).on('keyup', '.received_quantity', function () {
                        var obj=$(this);
                        var receive_quantity= parseFloat($(this).val());
                        var quantity= parseFloat(obj.closest('tr').find('.quantity').val());
//                        console.log(quantity);
                        if(receive_quantity>quantity)
                        {
                            alert('Received quantity can\'t be Grater than Quantity ');
                            $(this).val("");
                        }

                    });
                    $(document).on('change', '.unit_price', function () {
                        findRowTotal(this);
                        findTotal();
                    });
                    $(document).on('change', '#transportation_cost', function () {
                        findRowTotal(this);
                        findTotal();
                    });
                    $(document).on('submit', 'form', function (ee) {
                        var total = parseFloat($('#total_amount').val());
                        var paid = parseFloat($('#paid').val());
                        if (!total)
                            alert('Total Amount Required');
                        if (total < paid) {
                            alert('Paid amount can\'t be Grater than Total Amount ');
                            ee.preventDefault();
                        }
                    });
                    $(document).on('change', '.material_id', function () {
                        var currentMaterial = $(this).val();
                        var allMaterials = $('.material_id option:selected[value=' + currentMaterial + ']');
                        var noOfObject = allMaterials.toArray().length;
                        if (noOfObject > 1) {
                            $(this).val('');
                            alert('This Material already exists');
                        }
                    });
                    function findRowTotal(ele) {
                        var thisQuantity = parseFloat($(ele).closest('.purchase_row').find('.quantity').val());
                        var thisUnitPrice = parseFloat($(ele).closest('.purchase_row').find('.unit_price').val());
                        $(ele).closest('.purchase_row').find('.row_total').html(thisQuantity * thisUnitPrice);
                    }
                    function findTotal() {
                        var total = 0;
                        $.each($('.row_total'), function () {
                            if (parseFloat($(this).html()))
                                total += parseFloat($(this).html());
                        });
                        total += parseFloat($('#transportation_cost').val());
                        if (total)
                            $('#total_amount').val(total);
                        $('#total_amount').css('background', '#F7F779');
                        setTimeout(function () {
                            $('#total_amount').css('background', 'none');
                        }, 300);
                    }
                </script>