<div class="row">
    <div class="col-md-12">
        <div class="portlet box green-seagreen">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-globe"></i>
                    {{ ($person_type==1)?'Debtors List':'Creditor List' }}
                </div>
                <div>
                    <a style="margin: 7px; padding: 5px;" onclick="print_rpt()" class="btn btn-circle btn-danger pull-right" href="#">Print</a>
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
                                    Name
                                </th>
                                <th>
                                    Type
                                </th>
                                <th>
                                    Contact No.
                                </th>
                                <th class="text-center">
                                    Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(sizeof($persons)>0)
                            @foreach($persons as $key=>$person)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $person->person_name }}</td>
                                    <td>
                                        @if($person->person_type==1)
                                            {{ 'Employee' }}
                                        @elseif($person->person_type==2)
                                            {{ 'Supplier' }}
                                        @elseif($person->person_type==3)
                                            {{ 'Customer' }}
                                        @elseif($person->person_type==4)
                                            {{ 'Provider' }}
                                        @endif
                                    </td>
                                    <td>{{ $person->person_contact }}</td>
                                    <td class="text-center">{{ $person->balance }}</td>
                                </tr>
                            @endforeach
                                <tr>
                                    <th>Total</th>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <th class="text-center">{{ collect($persons)->sum('balance') }}</th>
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
