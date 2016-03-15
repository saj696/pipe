{!! csrf_field() !!}

<table class="table table-bordered">
    <tr>
        <th>
            <div class="form-group">
                {{ Form::label('date', 'Date', ['class'=>'col-md-4 control-label input_date']) }}
                <div class="col-md-4{{ $errors->has('date') ? ' has-error' : '' }}">
                    {{ Form::text('date', null,['class'=>'form-control col-md-2']) }}
                    @if ($errors->has('date'))
                        <span class="help-block">
                            <strong>{{ $errors->first('date') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </th>
    </tr>
</table>

<div class="row" id="wrapper" data-current-index="0">
    <div class="col-md-offset-1 col-md-10 main_row">
        <table class="table table-bordered">
            <tr>
                <td width="25%">
                    {{ Form::select('product_id[]', $products, null,['class'=>'form-control product_id', 'id'=>'product_id', 'placeholder'=>'Select Product', 'required'=>'required']) }}
                </td>
                <td width="20%">
                    {{ Form::text('production[]', null,['class'=>'form-control quantity production', 'required'=>'required', 'placeholder'=>'Input Production Quantity']) }}
                </td>
                <td width="2%">
                    <i class="fa fa-close" onclick="closeIt(this)" style="color: red; margin-top: 10px; cursor: pointer;"></i>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row text-center">
    <button type="button" onclick="addMore()" class="btn-circle btn btn-success" style="margin: 0px 0 20px 0">
        <img style="width: 15px; height: 15px;" src="{{ URL::asset('public/image/plus.png') }}" />
    </button>
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

        $(document).on("change", ".product_id", function () {
            var mat_attr = $(this).closest('#wrapper').find('.product_id');
            var arr = [];
            mat_attr.each(function () {
                arr.push($(this).val());
            });

            if (arr.length != arrUnique(arr).length) {
                $(this).val('');
                alert('Duplicate Product!');
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
        $('#wrapper:last').find('.product_id:last').attr('name', 'product_id[]');
        $('#wrapper:last').find('.production:last').attr('name', 'production[]');
        $('#wrapper:last').find('.product_id:last').val('');
        $('#wrapper:last').find('.production:last').val('');
    }

    function closeIt(ele) {
        var noOfChild = $('#wrapper .main_row').length;
        if (noOfChild < 2) {
            alert('You Can\'t remove all items!');
            return false;
        }
        ele.closest('.main_row').remove();
    }
</script>