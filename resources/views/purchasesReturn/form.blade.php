{!! csrf_field() !!}
<div class="form-group{{ $errors->has('supplier_id') ? ' has-error' : '' }}">
    {{ Form::label('supplier_id', 'Supplier', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('supplier_id',$suppliers, null,['class'=>'form-control']) }}
        @if ($errors->has('supplier_id'))
            <span class="help-block">
                <strong>{{ $errors->first('supplier_id') }}</strong>
            </span>
        @endif
    </div>
</div>
<div id="supplier_info">
</div>

<div class="form-group{{ $errors->has('purchase_return_date') ? ' has-error' : '' }}">
    {{ Form::label('purchase_return_date', 'Purchase Return Date', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('purchase_return_date', null,['class'=>'form-control datepicker']) }}
        @if ($errors->has('purchase_return_date'))
            <span class="help-block">
                <strong>{{ $errors->first('purchase_return_date') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group{{ $errors->has('transportation_cost') ? ' has-error' : '' }}">
    {{ Form::label('transportation_cost', 'Transportation Cost', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::text('transportation_cost', null,['class'=>'form-control']) }}
        @if ($errors->has('transportation_cost'))
            <span class="help-block">
                <strong>{{ $errors->first('transportation_cost') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group{{ $errors->has('return_type') ? ' has-error' : '' }}">
    {{ Form::label('return_type', 'Return Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7">
        {{ Form::select('return_type',Config::get('common.product_return_type') ,null,['class'=>'form-control']) }}
        @if ($errors->has('return_type'))
            <span class="help-block">
                <strong>{{ $errors->first('return_type') }}</strong>
            </span>
        @endif
    </div>
</div>
<?php
      $old_items = isset($purchase) ?  $purchase['purchaseDetails'] : false;
?>
<div class="row" id="purchase_wrp" data-current-index="{{ isset($purchase) ?  count($purchase['purchaseDetails']) : 0}}">
    @if($old_items)
        @foreach($old_items as $item)
            <div class="col-md-12 purchase_row">
                <div class="col-md-5">
                    <div class="form-group">
                        {{ Form::label('material_id', 'Material', ['class'=>'col-md-4 control-label']) }}
                        <div class="col-md-8">
                            {{ Form::select('items['.$item['id'].'][material_id]',$materials, $item['material_id'],['class'=>'form-control material_id','id'=>'','required']) }}
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        {{ Form::label('quantity', 'Quantity', ['class'=>'col-md-4 control-label']) }}
                        <div class="col-md-8">
                            {{ Form::text('items['.$item['id'].'][quantity]', $item['quantity'],['class'=>'form-control quantity','id'=>'','required']) }}
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        {{ Form::label('unit_price', 'Unit Price', ['class'=>'col-md-4 control-label']) }}
                        <div class="col-md-8">
                            {{ Form::text('items['.$item['id'].'][unit_price]', $item['unit_price'],['class'=>'form-control unit_price','id'=>'','required']) }}
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="quantity" class="col-md-4 control-label">Total</label>
                    <div class="col-md-4">
                        <span class="badge badge-info row_total" style="margin-top: 10px">{{$item['unit_price']*$item['quantity']}}</span>
                    </div>
                    <div class="col-md-4">
                        <i class="fa fa-close" onclick="closeIt(this)" style="color: red;margin-top: 10px; cursor: pointer"></i>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="col-md-12 purchase_row">
            <div class="col-md-3">
                <div class="form-group">
                    {{ Form::label('material_id', 'Material', ['class'=>'col-md-4 control-label']) }}
                    <div class="col-md-8">
                        <select name="items[0][material_id]" required="required" class="form-control material_id">
                            @foreach($materials as $material_id=>$material)
                            <option value="{{$material_id}}" data-raw-stock="{{$raw_stock[$material_id]}}">{{$material}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="col-md-11">Material in Stock</div>
                <div class="col-md-1 material_in_stock"> <span class="badge badge-danger"></span></div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    {{ Form::label('quantity', 'Quantity', ['class'=>'col-md-4 control-label']) }}
                    <div class="col-md-8">
                        {{ Form::text('items[0][quantity]', null,['class'=>'form-control quantity','id'=>'','required']) }}
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    {{ Form::label('unit_price', 'Unit Price', ['class'=>'col-md-4 control-label']) }}
                    <div class="col-md-8">
                        {{ Form::text('items[0][unit_price]', null,['class'=>'form-control unit_price','id'=>'','required']) }}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <label for="quantity" class="col-md-4 control-label">Total</label>
                <div class="col-md-4">
                    <span class="badge badge-info row_total" style="margin-top: 10px">0</span>
                </div>
                <div class="col-md-4">
                    <i class="fa fa-close" onclick="closeIt(this)" style="color: red;margin-top: 10px; cursor: pointer"></i>
                </div>
            </div>
        </div>
        @endif

</div>
<button type="button" onclick="addMore()" class="btn-circle btn btn-success pull-right" style="margin: 10px 0">Add more</button>

<div class="row">
    <div class="col-md-3 col-md-offset-9">
        <div class="form-group">
            <label class="col-md-5 control-label" for="paid">Total Amount</label>
            <div class="col-md-7">
                <input type="text" name="total" class="form-control" id="total_amount">
            </div>
        </div>
    </div>
    <div id="due_cash_wrp">

    </div>
</div>
<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-5 col-md-9">
            {{ Form::submit($submitText, ['class'=>'btn green','id'=>'submit']) }}
        </div>
    </div>
</div>
<script type="text/javascript">
    $('.material_id').val('')
    $(document).on('ready',function(){
        findTotal();
    });
    $(function() {
        $( ".datepicker" ).datepicker();
    });
    $(document).on('change','.material_id',function(){
        var raw_stock = $(this).find(':selected').data('raw-stock');
        $(this).closest('.purchase_row').find('.material_in_stock .badge').html(raw_stock);
    });
    function addMore()
    {
        var currentIndex = parseInt($('#purchase_wrp').data('current-index'));
        var newIndex = currentIndex+1;
        parseInt($('#purchase_wrp').data('current-index',newIndex));

        var rowHtml = $('#purchase_wrp :first').clone();
        $('#purchase_wrp').append(rowHtml);
        $('#purchase_wrp:last').find('.material_id:last').attr('name','items['+newIndex+'][material_id]');
        $('#purchase_wrp:last').find('.quantity:last').attr('name','items['+newIndex+'][quantity]');
        $('#purchase_wrp:last').find('.received_quantity:last').attr('name','items['+newIndex+'][received_quantity]');
        $('#purchase_wrp:last').find('.unit_price:last').attr('name','items['+newIndex+'][unit_price]');
        $('#purchase_wrp:last').find('.material_id:last').val('');
        $('#purchase_wrp:last').find('.quantity:last').val('');
        $('#purchase_wrp:last').find('.received_quantity:last').val('');
        $('#purchase_wrp:last').find('.unit_price:last').val('');
        $('#purchase_wrp:last').find('.row_total:last').html('');
    }
    function closeIt(ele)
    {
        var noOfChild = $('#purchase_wrp .purchase_row').length;
        if(noOfChild <2)
        {
            alert('You Can\'t remove all items');
            return false;
        }
        ele.closest('.purchase_row').remove();
        findTotal();
    }
    $(document).on('keyup','.quantity',function(){
        findRowTotal(this);
        checkTheStock(this);
        findTotal();
    });
    $(document).on('change','.unit_price',function(){
        findRowTotal(this);
        findTotal();
    });
    $(document).on('change','#transportation_cost',function(){
        findRowTotal(this);
        findTotal();
    });
    $(document).on('change','#return_type',function(){
        var supplier_id = $('#supplier_id').val();
        $('#due_cash_wrp').html('');
        $('#supplier_info').html('');
        if($(this).val() ==2)
        {
            getAndSetBalance(supplier_id);
        }
        else if($(this).val() ==3)
        {
            getAndSetDue(supplier_id);
        }
        else if($(this).val() ==4)
        {
            getAndSetBalance(supplier_id);
            var html = '<div class="col-md-3 col-md-offset-9">'+
                    '<div class="form-group">'+
                '<label class="col-md-5 control-label" for="paid">Pay Due Amount</label>'+
                ' <div class="col-md-7">'+
                '<input type="text" name="pay_due_amount" class="form-control" id="pay_due_amount">'+
                '</div>'+
                '</div>'+
                '</div>'+
                '<div class="col-md-3 col-md-offset-9">'+
                '<div class="form-group">'+
                '<label class="col-md-5 control-label" for="paid">Cash Return Amount</label>'+
                '<div class="col-md-7">'+
                '<input type="text" name="cash_return_amount" class="form-control" id="cash_return_amount">'+
                '</div>'+
                '</div>'+
                '</div>';
            $('#due_cash_wrp').html(html);

        }
    });
    $(document).on('change','.material_id',function(){
        var currentMaterial = $(this).val();
        var allMaterials = $('.material_id option:selected[value='+currentMaterial+']');
        var noOfObject = allMaterials.toArray().length;
        if(noOfObject>1){
            $(this).val('');
            alert('This Material already exists');
        }
    });
    $(document).on('submit','form',function(ee){
        var supplierDue = parseFloat($('.supplier_due').html());
        var total = parseFloat($('#total_amount').val());
        if($('#return_type').val() == 2 && supplierDue < total)
        {
            alert('Total amount can not be grater than due amount. Please select the Return type "Pay Due and Cash Return"');
            ee.preventDefault();
        }
    });
    function getAndSetBalance(supplier_id){
        $.ajax({
            url: '{{ route('ajax.get_person_balance_amount') }}',
            type: 'POST',
            dataType: "JSON",
            data:{
                person_id:supplier_id,
                person_type:'{{Config::get('common.person_type_supplier')}}'
            },
            success: function (data, status)
            {
                var html = '<div class="form-group">'+
                        '<label class="col-md-3 control-label">Supplier Due(Paid to us)</label>'+
                        '<div class="col-md-7">'+
                        '<div class="badge badge-danger supplier_due">'+
                        data
                '</div>'+
                '</div>'+
                '</div>'
                $('#supplier_info').html(html);
            },
            error: function (xhr, desc, err)
            {
                console.log("error");
            }
        });
    }
    function getAndSetDue(supplier_id){
        $.ajax({
            url: '{{ route('ajax.get_person_due_amount') }}',
            type: 'POST',
            dataType: "JSON",
            data:{
                person_id:supplier_id,
                person_type:'{{Config::get('common.person_type_supplier')}}'
            },
            success: function (data, status)
            {
                var html = '<div class="form-group">'+
                        '<label class="col-md-3 control-label">Supplier Balance(due to us)</label>'+
                        '<div class="col-md-7">'+
                        '<div class="badge badge-success supplier_blance">'+
                        data
                '</div>'+
                '</div>'+
                '</div>'
                $('#supplier_info').html(html);
            },
            error: function (xhr, desc, err)
            {
                console.log("error");
            }
        });
    }
    function checkTheStock(ele)
    {
        var rawStock = parseFloat($(ele).closest('.purchase_row').find('.material_id :selected').data('raw-stock'));
        if(!rawStock)
        {
            alert('Select the material first');
            $(ele).val('');
        }

        if(rawStock < parseFloat(ele.value))
        {
            alert('Quantity can\'t be greater then Stock');
            $(ele).val('');
        }
        console.log(rawStock)
    }
    function findRowTotal(ele){
        var thisQuantity = parseFloat($(ele).closest('.purchase_row').find('.quantity').val());
        var thisUnitPrice = parseFloat($(ele).closest('.purchase_row').find('.unit_price').val());
        $(ele).closest('.purchase_row').find('.row_total').html(thisQuantity*thisUnitPrice);
    }
    function findTotal(){
        var total = 0;
        $.each($('.row_total'),function(){
            if(parseFloat($(this).html()))
            total+= parseFloat($(this).html());
        });
        total+= parseFloat($('#transportation_cost').val());
        if(total)
        $('#total_amount').val(total);
        $('#total_amount').css('background','#F7F779');
        setTimeout(function () {
            $('#total_amount').css('background','none');
        }, 300);
    }
</script>