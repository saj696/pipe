<?php
$row = sizeof($debits);
if ($row < sizeof($credits)) {
    $row = sizeof($credits);
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
                    <a style="margin: 7px; padding: 5px;" onclick="print_rpt()"
                       class="btn btn-circle btn-danger pull-right" href="#">Print</a>
                </div>
            </div>
            <div class="portlet-body form">
                <div class="form-horizontal" role="form">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="portlet box green">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-cogs"></i>Debits
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
                                                        Date
                                                    </th>
                                                    <th>
                                                        Amount
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(sizeof($debits)>0)
                                                    @for($i=0; $i< $row; $i++)
                                                        <tr>
                                                            <td>{!! (sizeof($debits)>$i)? $i+1 : "&nbsp" !!}</td>
                                                            <td>{!! isset($debits[$i]['name'])? $debits[$i]['name'] : "&nbsp" !!}</td>
                                                            <td>{!! isset($debits[$i]['date'])?date('d-m-Y',$debits[$i]['date']) : "&nbsp" !!}</td>
                                                            <td>{!! isset($debits[$i]['amount'])? $debits[$i]['amount'] : "&nbsp" !!}</td>
                                                        </tr>
                                                    @endfor
                                                    <tr>
                                                        <th colspan="3">Total</th>
                                                        <th>{!! array_sum(array_column($debits,'amount'))? array_sum(array_column($debits,'amount')) : "&nbsp" !!}</th>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td colspan="12" class="text-center danger">No Data
                                                            Found
                                                        </td>
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
                                            <i class="fa fa-coffee"></i>Credits
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
                                                        Date
                                                    </th>
                                                    <th>
                                                        Amount
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(sizeof($credits)>0)
                                                    @for($i=0; $i< $row; $i++)
                                                        <tr>
                                                            <td>{!! (sizeof($credits)>$i)? $i+1 : "&nbsp" !!}</td>
                                                            <td>{!! isset($credits[$i]['name'])? $credits[$i]['name'] : "&nbsp" !!}</td>
                                                            <td>{!! isset($credits[$i]['date'])?date('d-m-Y',$credits[$i]['date']) : "&nbsp" !!}</td>
                                                            <td>{!! isset($credits[$i]['amount'])? $credits[$i]['amount'] : "&nbsp" !!}</td>
                                                        </tr>
                                                    @endfor
                                                    <tr>
                                                        <th colspan="3">Total</th>
                                                        <th>{!! array_sum(array_column($credits,'amount'))? array_sum(array_column($credits,'amount')) : "&nbsp" !!}</th>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td colspan="12" class="text-center danger">No Data
                                                            Found
                                                        </td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>
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
