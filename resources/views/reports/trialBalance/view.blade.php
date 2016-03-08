<div class="row" id="printArea">
    <div class="col-md-12">
        <div class="portlet box green-seagreen">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-globe"></i>Report View
                </div>
                <div>
                    <a style="margin: 7px; padding: 5px;" onclick="print_rpt()" class="btn btn-circle btn-danger pull-right"  href="#">Print</a>
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
                                                    <?php $totalDebit = 0;?>
                                                    @foreach($debits as $key=>$debit)
                                                        <tr>
                                                            <td>{{ $key+1 }}</td>
                                                            <td>{{ $debit->name }}</td>
                                                            <td>{{ date('d-m-Y', $debit->date) }}</td>
                                                            <td>{{ $debit->amount }}</td>
                                                        </tr>
                                                        <?php $totalDebit += $debit->amount;?>
                                                    @endforeach
                                                    <tr>
                                                        <td colspan="3">Total</td>
                                                        <td>{{ $totalDebit }}</td>
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
                                                    <?php $totalCredit = 0;?>
                                                    @foreach($credits as $key=>$credit)
                                                        <tr>
                                                            <td>{{ $key+1 }}</td>
                                                            <td>{{ $credit->name }}</td>
                                                            <td>{{ date('d-m-Y', $credit->date) }}</td>
                                                            <td>{{ $credit->amount }}</td>
                                                        </tr>
                                                        <?php $totalCredit += $credit->amount;?>
                                                    @endforeach
                                                    <tr>
                                                        <td colspan="3">Total</td>
                                                        <td>{{ $totalCredit }}</td>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
