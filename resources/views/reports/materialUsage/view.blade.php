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

            <div class="portlet-body form" id="printArea">
                <div class="form-horizontal" role="form">
                    <div class="form-body">
                        <div class="table-scrollable">
                            <table class="table table-bordered">
                                @if(sizeof($arrangedArray)>0)
                                    <tr>
                                        <td>Date</td>
                                        @for($i=0; $i<sizeof($uniqueMaterials); $i++)
                                            <td>
                                                {{ $uniqueMaterials[$i] }}
                                            </td>
                                        @endfor
                                    </tr>
                                    @foreach($arrangedArray as $key=>$arranged)
                                        <tr>
                                            <td>{{ $key }}</td>
                                            @foreach($uniqueMaterials as $uniqueMaterial)
                                                <td>{{ isset($arranged[$uniqueMaterial])?$arranged[$uniqueMaterial]:0 }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="12" class="text-center danger">No Data Found</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
