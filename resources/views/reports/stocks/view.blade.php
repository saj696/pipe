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

            <div class="portlet-body" id="printArea">
                <div class="table-scrollable">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>
                                Serial
                            </th>
                            <th>
                                {{ $stock_type==1?'Material Name':'Product Name' }}
                            </th>
                            <th>
                                Year
                            </th>
                            <th>
                                Quantity
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(sizeof($stocks)>0)
                            @foreach($stocks as $key=>$stock)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $stock_type==1?$stock->name:$stock->title }}</td>
                                    <td>{{ $stock->year }}</td>
                                    <td>{{ $stock->quantity }}</td>
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
