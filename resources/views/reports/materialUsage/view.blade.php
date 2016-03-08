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
                        <div class="table-scrollable">
                            <table class="table table-bordered">
                                <tr>
                                    <td>&nbsp;</td>
                                @for($i=0; $i<sizeof($uniqueMaterials); $i++)
                                    <td>
                                        {{ $uniqueMaterials[$i] }}
                                    </td>
                                @endfor
                                </tr>
                                @foreach($arrangedArray as $key=>$arranged)
                                    <tr>
                                        <td>{{ $key }}</td>
                                        @foreach($arranged as $data)
                                            <td>{{ $data }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
