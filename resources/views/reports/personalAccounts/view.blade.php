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
                                @if($person_type==1)
                                    {{ 'Employee name' }}
                                @elseif($person_type==2)
                                    {{ 'Supplier name' }}
                                @elseif($person_type==3)
                                    {{ 'Customer name' }}
                                @endif
                            </th>
                            <th>
                                Contact No.
                            </th>
                            <th class="text-center">
                                Balance
                            </th>
                            <th class="text-center">
                                Due
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(sizeof($persons)>0)
                            @foreach($persons as $key=>$person)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $person->person_name }}</td>
                                    <td>{{ $person->phone }}</td>
                                    <td class="text-center">{{ $person->balance }}</td>
                                    <td class="text-center">{{ $person->due }}</td>
                                </tr>
                            @endforeach
                                <tr>
                                    <th>Total</th>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <th class="text-center">{{ collect($persons)->sum('balance') }}</th>
                                    <th class="text-center">{{ collect($persons)->sum('due') }}</th>
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
