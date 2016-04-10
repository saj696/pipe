<div class="row">
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

            <div id="printArea" class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr><td colspan="3"><b>Sumon PVC Pipe</b></td></tr>
                            <tr><td colspan="3"><b>Income Statement</b></td></tr>
                            <tr><td colspan="3"><b>{{ 'For the period ending '. date('jS F, Y', $ending_date) }}</b></td></tr>
                            <tr><td colspan="3">&nbsp;</td></tr>
                            <tr><td colspan="3"><b>Revenues</b></td></tr>
                            @if(sizeof($revenues)>0)
                            @foreach($revenues as $revenue)
                                <tr><td>{{ $revenue->name }}</td><td>{{ $revenue->sum_amount }}</td><td>&nbsp;</td></tr>
                            @endforeach
                            <tr><td colspan="2">Total Revenue</td><td>{{ App\Helpers\CommonHelper::get_revised_balance_for_contra($revenues) }}</td></tr>
                            @else
                                <td colspan="12" class="text-center danger">No Data Found</td>
                            @endif
                            <tr><td colspan="3">&nbsp;</td></tr>
                            <tr><td colspan="3"><b>Expenses</b></td></tr>
                            @if(sizeof($expenses)>0)
                            @foreach($expenses as $expense)
                                <tr><td>{{ $expense->name }}</td><td>{{ $expense->sum_amount }}</td><td>&nbsp;</td></tr>
                            @endforeach
                            <tr><td colspan="2">Total Expenses</td><td>{{ App\Helpers\CommonHelper::get_revised_balance_for_contra($expenses) }}</td></tr>
                            @else
                                <td colspan="12" class="text-center danger">No Data Found</td>
                            @endif
                            <tr><td colspan="3">&nbsp;</td></tr>
                            <tr><td colspan="2"><b>Net Income</b></td><td>{{ App\Helpers\CommonHelper::get_revised_balance_for_contra($revenues)-App\Helpers\CommonHelper::get_revised_balance_for_contra($expenses) }}</td></tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
