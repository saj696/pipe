<script src="{{ URL::asset('js/ui-toastr.js') }}" type="text/javascript"></script>
{!! csrf_field() !!}
<input type="hidden" , name="sales_order_id" value="{{ $id }}">
<table class="table table-bordered">
    <tr>
        <th>Product Name</th>
        <th>Quantity</th>
        <th>Delivered</th>
        <th>Remaining</th>
        <th>Stock Quantity</th>
        <th>Deliver Now</th>
    </tr>
    @foreach($productLists as $productList)
        <tr class="product">
            <td>{{  $productList->title }}</td>
            <td>
                {{  $productList->sales_quantity }}
                <input type="hidden" name="quantity[{{ $productList->product_id }}]"
                       value="{{ $productList->sales_quantity }}">
            </td>
            <td>{{  $productList->delivered_quantity ? $productList->delivered_quantity : 0 }}</td>
            <td class="remaining">{{  ($productList->sales_quantity-$productList->delivered_quantity) }}</td>
            <td>
                @if($productList->quantity > 10)
                    <span class="badge badge-success stock">{{  ($productList->quantity) }}</span>
                @else
                    <span class="badge badge-danger stock">{{  ($productList->quantity) }}</span>
                @endif
            </td>
            <td>{{  Form::text('deliver_now['.$productList->product_id.']', (($productList->sales_quantity-$productList->delivered_quantity)==0)? 0: "", ['class'=>'form-control deliver_now','required'=>'required']) }}</td>
        </tr>
    @endforeach
</table>
<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-5 col-md-7">
            {{ Form::submit('Deliver', ['class'=>'btn green']) }}
        </div>
    </div>
</div>

<script>
    $(document).ready(function (e) {
        $(document).on('keyup', '.deliver_now', function () {
            var deliver_now = $(this).val();
            var obj = $(this);

            var remaining = parseFloat(obj.closest('.product').find('.remaining').html());
            var stock = parseFloat(obj.closest('.product').find('.stock').html());
//            console.log(stock)
            if (deliver_now > stock || deliver_now < 0) {
                toster('Deliver quantity should be equal or less than stock quantity !');
                $(this).val("");

            }
            else if (deliver_now > remaining || deliver_now < 0) {
                toster('Deliver quantity should be equal or less than remaining quantity !');
                $(this).val("");
            }
        })
    });

    function Toast(type, css, msg) {
        this.type = type;
        this.css = css;
        this.msg = 'Deliver quantity should be equal or less than remaining quantity !';
    }

    var toasts = [

        new Toast('error', 'toast-bottom-right', ''),

    ];

    toastr.options.positionClass = 'toast-top-full-width';
    toastr.options.extendedTimeOut = 0; //1000;
    toastr.options.timeOut = 5000;
    toastr.options.fadeOut = 250;
    toastr.options.fadeIn = 250;

    var i = 0;

    function toster(msg) {
        delayToasts(msg);
    }
    ;

    function delayToasts(msg) {
        if (i === toasts.length) {
            return;
        }
        var delay = i === 0 ? 0 : 2100;
        window.setTimeout(function () {
            showToast(msg);
        }, delay);

        // re-enable the button
        if (i === toasts.length - 1) {
            window.setTimeout(function () {
                i = 0;
            }, delay + 1000);
        }
    }

    function showToast(msg) {
        var t = toasts[i];
        toastr.options.positionClass = t.css;
        toastr[t.type](msg);
        i++;
        delayToasts();
    }

</script>