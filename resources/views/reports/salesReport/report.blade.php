<div class="row">
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
            <div id="printArea" class="portlet-body form">

                <div class="table-scrollable">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>
                                Sl.
                            </th>
                            <th>
                                Date
                            </th>
                            <th>
                                Workspace
                            </th>
                            <th>
                                Sales Type
                            </th>
                            <th>
                                Customer
                            </th>
                            <th>
                                Total
                            </th>
                            <th>
                                Discount
                            </th>
                            <th>
                                Net
                            </th>
                            <th>
                                Paid
                            </th>
                            <th>
                                Due
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($results))
                            <?php $i = 1;?>
                            @foreach($results as $result)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $result['date'] }}</td>
                                    <td>{{ $result['workspace'] }}</td>
                                    <td>{{ $result['sales_type'] }}</td>
                                    <td>{{ $result['customer'] }}</td>
                                    <td>{{ $result['total'] }}</td>
                                    <td>{{ $result['discount'] }}</td>
                                    <td>{{ $result['net'] }}</td>
                                    <td>{{ $result['paid'] }}</td>
                                    <td>{{ $result['due'] }}</td>
                                </tr>
                            @endforeach

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
