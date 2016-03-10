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

            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr><td colspan="3">Sumon PVC Pipe</td></tr>
                            <tr><td colspan="3">Income Statement</td></tr>
                            <tr><td colspan="3">{{ 'For the period ending '. date('jS F, Y', $ending_date) }}</td></tr>
                            <tr><td colspan="3">&nbsp;</td></tr>
                            <tr><td colspan="3">Revenues</td></tr>
                            @if(sizeof($revenues)>0)
                            @foreach($revenues as $revenue)
                                <tr><td>{{ $revenue->name }}</td><td>{{ $revenue->sum_amount }}</td><td>&nbsp;</td></tr>
                            @endforeach
                            <tr><td colspan="2">Total Revenue</td><td>{{ App\Helpers\CommonHelper::get_revised_balance_for_contra($revenues) }}</td></tr>
                            @else
                                <td colspan="12" class="text-center danger">No Data Found</td>
                            @endif
                            <tr><td colspan="3">&nbsp;</td></tr>
                            <tr><td colspan="3">Expenses</td></tr>
                            @if(sizeof($expenses)>0)
                            @foreach($expenses as $expense)
                                <tr><td>{{ $expense->name }}</td><td>{{ $expense->sum_amount }}</td><td>&nbsp;</td></tr>
                            @endforeach
                            <tr><td colspan="2">Total Expenses</td><td>{{ App\Helpers\CommonHelper::get_revised_balance_for_contra($expenses) }}</td></tr>
                            @else
                                <td colspan="12" class="text-center danger">No Data Found</td>
                            @endif
                            <tr><td colspan="3">&nbsp;</td></tr>
                            <tr><td colspan="2">Net Income</td><td>{{ App\Helpers\CommonHelper::get_revised_balance_for_contra($revenues)-App\Helpers\CommonHelper::get_revised_balance_for_contra($expenses) }}</td></tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
