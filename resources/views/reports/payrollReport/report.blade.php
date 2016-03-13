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
                                Sl. No.
                            </th>
                            <th>
                                Year
                            </th>
                            <th>
                                Month
                            </th>
                            <th>
                                Workspace
                            </th>
                            <th>
                                Employee
                            </th>

                            <th>
                                Salary
                            </th>
                            <th>
                                Cut
                            </th>
                            <th>
                                Bonus
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
                        @if(!empty($salaries))
                            <?php
                            $month = array_flip(Config::get('common.month'));
                            $i = 1;
                            ?>
                            @foreach($salaries as $salary)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $salary->year }}</td>
                                    <td>{{  $month[str_pad($salary->month,2,0,STR_PAD_LEFT)] }}</td>
                                    <td>{{ $salary->workspace_name }}</td>
                                    <td>{{ $salary->employee_name }}</td>
                                    <td>{{ $salary->salary }}</td>
                                    <td>{{ $salary->cut }}</td>
                                    <td>{{ $salary->bonus }}</td>
                                    <td>{{ $salary->net }}</td>
                                    <td>{{ $salary->paid }}</td>
                                    <td>{{ $salary->due }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <th>{{ collect($salaries)->sum('salary') }}</th>
                                <th>{{ collect($salaries)->sum('cut') }}</th>
                                <th>{{ collect($salaries)->sum('bonus') }}</th>
                                <th>{{ collect($salaries)->sum('net') }}</th>
                                <th>{{ collect($salaries)->sum('paid') }}</th>
                                <th>{{ collect($salaries)->sum('due') }}</th>
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
