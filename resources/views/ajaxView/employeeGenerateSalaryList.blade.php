<table class="table table-bordered">
    <tr>
        <th><input type="checkbox" id="check_all"></th>
        <th>Name</th>
        <th>Salary</th>
        <th>Cut</th>
        <th>Overtime</th>
        <th>Overtime Amount</th>
        <th>Bonus</th>
        <th>Net</th>
    </tr>
    @if(sizeof($employees)> 0)
        @foreach($employees as $employee)
            <tr>
                <td><input type="checkbox" name="selected[{{ $employee->id }}]" class="e_select"
                           value="{{ $employee->id }}"></td>
                <td width="20%"> {{ $employee->name }} </td>
                <td> {{ $employee->designation->salary }} </td>
                <td><input type="text" name="employee[{{ $employee->id }}][cut]" placeholder="Cut"
                           class="form-control cut"></td>
                <td><input type="text" name="employee[{{ $employee->id }}][overtime]" placeholder="Over time"
                           class="form-control over_time" data-hourly_rate="{{ $employee->designation->hourly_rate }}">
                </td>
                <td><input type="text" name="employee[{{ $employee->id }}][overtime_amount]"
                           placeholder="Over time amount" class="form-control over_time_amount"></td>
                <td><input type="text" name="employee[{{ $employee->id }}][bonus]" placeholder="Bonus"
                           class="form-control bonus"></td>
                <td><input readonly type="text" name="employee[{{ $employee->id }}][net]" placeholder="Net"
                           class="form-control net" value="{{ $employee->designation->salary }}" required></td>
                <input type="hidden" class="salary" name="employee[{{ $employee->id }}][salary]"
                       value="{{ $employee->designation->salary }}">
                <input type="hidden" name="employee[{{ $employee->id }}][employee_type]"
                       value="{{ $employee->employee_type }}">
                <input type="hidden" name="employee[{{ $employee->id }}][workspace_id]"
                       value="{{ $employee->workspace_id }}">
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="8" style="text-align: center;color: #ff0000">Already ready generated salary or no data found.
            </td>
        </tr>
    @endif
</table>



