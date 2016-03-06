<table class="table table-bordered">
    <tr>
        <th><input type="checkbox" id="check_all"></th>
        <th>Name</th>
        <th>Net Salary</th>
        <th>Due</th>
        <th>Paid</th>
        <th>Pay Now</th>
    </tr>
    @if(sizeof($salaries)> 0)
        @foreach($salaries as $salary)
            <tr>
                <td><input type="checkbox" name="selected[{{ $salary->employee->id }}]" class="form-control e_select"
                           value="{{ $salary->employee->id }}"></td>
                <td width="20%"><input type="text" value="{{ $salary->employee->name }}" disabled
                                       class="form-control"></td>
                <td><input type="text" value="{{ $salary->net }}" disabled
                           class="form-control"></td>
                <td><input type="text" name="employee[{{ $salary->employee->id }}][due]" value="{{ $salary->due }}"
                           readonly
                           class="form-control due"></td>
                <td><input type="text" value="{{$salary->paid}}" disabled
                           class="form-control paid">
                </td>
                <td><input type="text" name="employee[{{ $salary->employee->id }}][pay_now]"
                           placeholder="Pay Now" value="{{ $salary->net-$salary->paid }}" class="form-control pay_now">
                </td>
                <input type="hidden" name="employee[{{ $salary->employee->id }}][salary_id]"
                       value="{{ $salary->id }}">
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="8" style="text-align: center;color: #ff0000">No data found.</td>
        </tr>
    @endif
</table>



