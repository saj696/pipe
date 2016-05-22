<div class="row">
    <div class="col-md-12">
        <div class="portlet box green-seagreen">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-globe"></i>Transaction Statement
                </div>
                <div>
                    <a style="margin: 7px; padding: 5px;" onclick="print_rpt()" class="btn btn-circle btn-danger pull-right" href="#">Print</a>
                </div>
            </div>
            <div id="printArea" class="portlet-body form">
                <div class="form-horizontal" role="form">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box blue-chambray">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            Year {{\App\Helpers\CommonHelper::get_current_financial_year()}} Opening Balance: {{ $balanceDueBeforeCurrentYear['balance'] }}
                                        </div>
                                        <div class="caption pull-right">
                                            Year {{\App\Helpers\CommonHelper::get_current_financial_year()}} Opening Due: {{ $balanceDueBeforeCurrentYear['due'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="portlet box yellow-casablanca">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-cogs"></i>Detail Statement
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="table-scrollable">
                                            <table class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th>
                                                        Date
                                                    </th>
                                                    <th>
                                                        Memo/ Receipt No.
                                                    </th>
                                                    <th>
                                                        Type
                                                    </th>
                                                    <th>
                                                        Total
                                                    </th>
                                                    <th>
                                                        Paid
                                                    </th>
                                                    <th>
                                                        Cash Returned
                                                    </th>
                                                    <th>
                                                        Replaced
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($statements as $date=>$detailData)
                                                    <tr style="border-top: 0px !important;"><td style="border-top: 0px !important;" colspan="12" class="pull-left"><b>{{ date('d-m-Y', $date) }}</b></td></tr>
                                                    @foreach($detailData as $key=>$data)
                                                        <tr>
                                                            <td></td>
                                                            <td>{{ isset($data['voucher_no'])?$data['voucher_no']:$data['memo_no'] }}</td>
                                                            <td>{{ $key }}</td>
                                                            <td>{{ isset($data['sum_total'])?$data['sum_total']:0 }}</td>
                                                            <td>{{ isset($data['sum_paid'])?$data['sum_paid']:0 }}</td>
                                                            <td>{{ isset($data['sum_cash'])?$data['sum_cash']:0 }}</td>
                                                            <td>{{ isset($data['sum_replacement'])?$data['sum_replacement']:'' }}</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td colspan="12">
                                                            <div class="portlet box light" style="margin-bottom: 0px !important;">
                                                                <div class="portlet-title">
                                                                    <div class="caption">
                                                                        Balance: {{ \App\Helpers\CommonHelper::customer_balance_due_after_date($person_id, $type, $date)['balance'] }}
                                                                    </div>
                                                                    <div class="caption pull-right">
                                                                        Due: {{ \App\Helpers\CommonHelper::customer_balance_due_after_date($person_id, $type, $date)['due'] }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="portlet box red">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            Balance: {{ \App\Helpers\CommonHelper::customer_balance_due_before_date($person_id, $type, strtotime(date('d-m-'.\App\Helpers\CommonHelper::get_current_financial_year())))['balance'] }}
                                        </div>
                                        <div class="caption pull-right">
                                            Due: {{ \App\Helpers\CommonHelper::customer_balance_due_before_date($person_id, $type, strtotime(date('d-m-'.\App\Helpers\CommonHelper::get_current_financial_year())))['due'] }}
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
