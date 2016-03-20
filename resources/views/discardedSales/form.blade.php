{!! csrf_field() !!}

<div class="form-group">
    {{ Form::label('date', 'Date', ['class'=>'col-md-4 control-label']) }}
    <div class="col-md-5{{ $errors->has('date') ? ' has-error' : '' }}">
        {{ Form::text('date', null,['class'=>'form-control col-md-2', 'required'=>'required']) }}
        @if ($errors->has('name'))
            <span class="help-block">
                <strong>{{ $errors->first('date') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('customer_id', 'Customer', ['class'=>'col-md-4 control-label']) }}
    <div class="col-md-5">
        {{ Form::select('customer_id', $customers, null,['class'=>'form-control', 'placeholder'=>'Select']) }}
    </div>
</div>

@if(isset($discardedSales->DiscardedSalesDetail))
    <div class="row" id="wrapper" data-current-index="{{ isset($discardedSales) ?  count($discardedSales->DiscardedSalesDetail) : 0}}">
    @foreach($discardedSales->DiscardedSalesDetail as $detail)
        <div class="col-md-offset-1 col-md-10 main_row">
            <table class="table table-bordered">
                <tr>
                    <td width="25%">
                        {{ Form::select('items['.$detail->id.'][material_id]', $materials, $detail->material_id,['class'=>'form-control material_id', 'id'=>'material_id', 'placeholder'=>'Select Material', 'required'=>'required']) }}
                    </td>
                    <td width="20%">
                        {{ Form::number('items['.$detail->id.'][sale_quantity]', $detail->quantity,['class'=>'form-control quantity sale_quantity', 'min'=>0,'step'=>0.1, 'required'=>'required', 'placeholder'=>'Input Sale Quantity']) }}
                    </td>
                    <td width="20%">
                        {{ Form::number('items['.$detail->id.'][amount]', $detail->amount,['class'=>'form-control quantity amount', 'min'=>0,'step'=>1, 'required'=>'required', 'placeholder'=>'Input Amount']) }}
                    </td>
                    <td width="2%">
                        <i class="fa fa-close" onclick="closeIt(this)" style="color: red; margin-top: 10px; cursor: pointer;"></i>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
    </div>
@else
<div class="row" id="wrapper" data-current-index="0">
    <div class="col-md-offset-1 col-md-10 main_row">
        <table class="table table-bordered">
            <tr>
                <td width="25%">
                    {{ Form::select('items[0][material_id]', $materials, null,['class'=>'form-control material_id', 'id'=>'material_id', 'placeholder'=>'Select Material', 'required'=>'required']) }}
                </td>
                <td width="20%">
                    {{ Form::number('items[0][sale_quantity]', null,['class'=>'form-control quantity sale_quantity', 'min'=>0,'step'=>0.1, 'required'=>'required', 'placeholder'=>'Input Sale Quantity']) }}
                </td>
                <td width="20%">
                    {{ Form::number('items[0][amount]', null,['class'=>'form-control quantity amount', 'min'=>0,'step'=>1, 'required'=>'required', 'placeholder'=>'Input Amount']) }}
                </td>
                <td width="2%">
                    <i class="fa fa-close" onclick="closeIt(this)" style="color: red; margin-top: 10px; cursor: pointer;"></i>
                </td>
            </tr>
        </table>
    </div>
</div>
@endif

<div class="row text-center">
    <button type="button" onclick="addMore()" class="btn-circle btn btn-success" style="margin: 0px 0 20px 0">
        <img style="width: 15px; height: 15px;" src="{{ URL::asset('public/image/plus.png') }}" />
    </button>
</div>

<div class="row">
    <div class="col-md-3 col-md-offset-9">
        <div class="form-group">
            <label class="col-md-5 control-label" for="paid">Total Amount</label>
            <div class="col-md-7">
                {{ Form::text('total_amount', null,['class'=>'form-control unit_price','readonly'=>'readonly','id'=>'total_amount','required']) }}
            </div>
        </div>
    </div>
    <div class="col-md-3 col-md-offset-9">
        <div class="form-group{{ $errors->has('paid') ? ' has-error' : '' }}">
            {{ Form::label('paid', 'Paid Amount', ['class'=>'col-md-5 control-label']) }}
            <div class="col-md-7">
                {{ Form::text('paid_amount', null,['class'=>'form-control quantity','id'=>'paid_amount']) }}
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
    $(function () {
        $("#date").datepicker({maxDate: new Date});
    });

    jQuery(document).ready(function () {
        $(document).on("keyup", ".quantity", function () {
            this.value = this.value.replace(/[^0-9\.]/g, '');
        });

        $(document).on("change", ".material_id", function () {
            var mat_attr = $(this).closest('#wrapper').find('.material_id');
            if(mat_attr.val()>0)
            {
                var arr = [];
            }

            mat_attr.each(function () {
                arr.push($(this).val());
            });

            if (arr.length != arrUnique(arr).length) {
                $(this).val('');
                alert('Duplicate Material!');
            }
            //console.log(arrUnique(arr));
        });
    });

    function arrUnique(a) {
        var t = [];
        for (var x = 0; x < a.length; x++) {
            if (t.indexOf(a[x]) == -1)t.push(a[x]);
        }
        return t;
    }

    function addMore() {
        var currentIndex = parseInt($('#wrapper').data('current-index'));
        var newIndex = currentIndex + 1;
        parseInt($('#wrapper').data('current-index', newIndex));

        var rowHtml = $('#wrapper :first').clone();
        $('#wrapper').append(rowHtml);
        $('#wrapper:last').find('.material_id:last').attr('name', 'items[' + newIndex + '][material_id]');
        $('#wrapper:last').find('.sale_quantity:last').attr('name', 'items[' + newIndex + '][sale_quantity]');
        $('#wrapper:last').find('.amount:last').attr('name', 'items[' + newIndex + '][amount]');
        $('#wrapper:last').find('.material_id:last').val('');
        $('#wrapper:last').find('.sale_quantity:last').val('');
        $('#wrapper:last').find('.amount:last').val('');
    }

    function closeIt(ele) {
        var noOfChild = $('#wrapper .main_row').length;
        if (noOfChild < 2) {
            alert('You Can\'t remove all items!');
            return false;
        }
        ele.closest('.main_row').remove();
    }

    $(document).on('keyup', '.amount', function () {
        var total = 0;
        $.each($('.amount'), function () {
            if (parseFloat($(this).val()))
            total += parseFloat($(this).val());
        });

        if (total)
        $('#total_amount').val(total);
        $('#total_amount').css('background', '#F7F779');
        setTimeout(function () {
            $('#total_amount').css('background', 'none');
        }, 300);
    });

    $(document).on('keyup', '#paid_amount', function () {
        var total = parseFloat($('#total_amount').val());
        if(parseFloat($(this).val())>total)
        {
            alert('Paid amount Can\'t be greater than total amount!');
            $(this).val('');
        }
    });
</script>