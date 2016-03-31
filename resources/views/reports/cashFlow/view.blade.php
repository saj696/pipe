<?php
$row = sizeof($sales);
if ($row < sizeof($expenses)) {
    $row = sizeof($expenses);
}
?>

<div class="row" id="printArea">
    <div class="col-md-12">
        <div class="portlet box green-seagreen">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-globe"></i>Report View
                </div>
                <div>
                    <a style="margin: 7px; padding: 5px;" onclick="print_rpt()" class="btn btn-circle btn-danger pull-right" href="#">Print</a>
                </div>
            </div>
            <div class="portlet-body form">
                <div class="form-horizontal" role="form">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box blue-chambray">
                                    <div class="portlet-title">
                                        <div class="caption pull-right">
                                            Cash Before Today: {{ $cashInHand+array_sum(array_column($expenses,'amount'))-array_sum(array_column($sales,'amount')) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="portlet box green">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-cogs"></i>Sales
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="table-scrollable">
                                            <table class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th>
                                                        Serial
                                                    </th>
                                                    <th>
                                                        Customer
                                                    </th>
                                                    <th>
                                                        Amount
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(sizeof($sales)>0)
                                                    @for($i=0; $i< $row; $i++)
                                                        <tr>
                                                            <td>{!! (sizeof($sales)>$i)? $i+1 : "&nbsp" !!}</td>
                                                            <td>
                                                                <?php
                                                                    if(isset($sales[$i]['customer_type']) && $sales[$i]['customer_type']==Config::get('common.person_type_employee'))
                                                                    {
                                                                        echo $employees[$sales[$i]['customer_id']];
                                                                    }
                                                                    elseif(isset($sales[$i]['customer_type']) && $sales[$i]['customer_type']==Config::get('common.person_type_supplier'))
                                                                    {
                                                                        echo $suppliers[$sales[$i]['customer_id']];
                                                                    }
                                                                    elseif(isset($sales[$i]['customer_type']) && $sales[$i]['customer_type']==Config::get('common.person_type_customer'))
                                                                    {
                                                                        echo $customers[$sales[$i]['customer_id']];
                                                                    }
                                                                    else
                                                                    {
                                                                        echo '&nbsp';
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td>{!! isset($sales[$i]['amount'])? $sales[$i]['amount'] : '&nbsp' !!}</td>
                                                        </tr>
                                                    @endfor
                                                    <tr>
                                                        <th colspan="2">Total (Excluding Sales Returns)</th>
                                                        <th>{!! array_sum(array_column($sales,'amount'))? array_sum(array_column($sales,'amount'))-array_sum(array_column($salesReturns,'amount')) : '&nbsp' !!}</th>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td colspan="12" class="text-center danger">No Data Found</td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="portlet box yellow">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-coffee"></i>Expenses
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="table-scrollable">
                                            <table class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th>
                                                        Serial
                                                    </th>
                                                    <th>
                                                        Account Head
                                                    </th>
                                                    <th>
                                                        Amount
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @if(sizeof($expenses)>0)
                                                    @for($i=0; $i< $row; $i++)
                                                        <tr>
                                                            <td>{!! (sizeof($expenses)>$i)? $i+1 : "&nbsp" !!}</td>
                                                            <td>{!! isset($expenses[$i]['name'])? $expenses[$i]['name'] : "&nbsp" !!}</td>
                                                            <td>{!! isset($expenses[$i]['amount'])? $expenses[$i]['amount'] : 0 !!}</td>
                                                        </tr>
                                                    @endfor
                                                    <tr>
                                                        <th colspan="2">Total</th>
                                                        <th>{!! array_sum(array_column($expenses,'amount'))? array_sum(array_column($expenses,'amount')) : 0 !!}</th>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td colspan="12" class="text-center danger">No Data Found</td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="portlet box blue-chambray">
                                    <div class="portlet-title">
                                        <div class="caption pull-right">
                                            Cash In Hand: {{ $cashInHand }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
