@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green-seagreen">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Invoice
                    </div>
                    <div>
                        <a style="margin: 7px; padding: 5px;" onclick="print_rpt()"
                           class="btn btn-circle btn-danger pull-right" href="#">Print</a>
                    </div>
                </div>
                <div id="printArea" class="portlet-body">

                    <h2 class="text-center" style="margin-bottom: 40px !important;">SUMON PVC PIPE</h2>
                        <span class="" style="font-size: 16px">
                            Name: {{ App\Helpers\CommonHelper::getCustomerName($salesOrder->customer_id,$salesOrder->customer_type) }}</span>
                        <span class="pull-right">
                            Date: {{ date('d-m-Y',$salesOrder->created_at) }}
                        </span>
                    <div class="table-scrollable" style="margin-top:10px !important;">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center">
                                    Sl.
                                </th>
                                <th class="text-center">
                                    Product Name
                                </th>
                                <th class="text-center">
                                    Quantity
                                </th>
                                <th class="text-center">
                                    Rate
                                </th>
                                <th class="text-center">
                                    Total
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($salesOrder))
                                @foreach($salesOrder->salesOrderItems as $key=>$salesOrderItem)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $salesOrderItem->product->title }}</td>
                                        <td class="text-right">{{ $salesOrderItem->sales_quantity }}{{ $salesOrderItem->sales_unit_type==1? " (ft)" : " (kg)" }}</td>
                                        <td class="text-right">{{ $salesOrderItem->unit_price }}</td>
                                        <td class="text-right">{{ $salesOrderItem->unit_price*$salesOrderItem->sales_quantity }}</td>
                                    </tr>
                                @endforeach
                                @if($salesOrder->transport_cost >0)
                                    <tr>

                                        <td colspan="4" class="text-right">Transportation Cost</td>
                                        <td class="text-right">{{ $salesOrder->transport_cost }}</td>
                                    </tr>
                                @endif

                                @if($salesOrder->labour_cost >0)
                                    <tr>

                                        <td colspan="4" class="text-right">Labour Cost</td>
                                        <td class="text-right">{{ $salesOrder->labour_cost }}</td>
                                    </tr>
                                @endif

                                @if($salesOrder->discount >0)
                                    <tr>
                                        <td colspan="4" class="text-right">Discount</td>
                                        <td class="text-right">{{ $salesOrder->discount }}</td>
                                    </tr>
                                @endif

                                @if($salesOrder->paid >0)
                                    <tr>
                                        <td colspan="4" class="text-right">Paid</td>
                                        <td class="text-right">{{ $salesOrder->paid }}</td>
                                    </tr>
                                @endif

                                @if($salesOrder->due >0)
                                    <tr>
                                        <td colspan="4" class="text-right">Due</td>
                                        <td class="text-right">{{ $salesOrder->due }}</td>
                                    </tr>
                                @endif

                                <tr>
                                    <td colspan="4" class="text-right" style="font-weight: bold">Grand Total</td>
                                    <td class="text-right" style="font-weight: bold">{{ $salesOrder->total }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="7" class="text-center danger">No Data Found</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection