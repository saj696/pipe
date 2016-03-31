<table class="table table-bordered">
    <tr>
        <th><input type="checkbox" id="check_all"></th>
        <th>Name</th>
        <th>Mobile</th>
        <th>Wage</th>
        <th>Pay Now</th>
    </tr>
    @if(sizeof($employees)> 0)
        @foreach($employees as $key=>$employee)
            <tr>
                <td><input type="checkbox" name="selected[{{ $key }}]" class="form-control e_select"
                           value="{{ $employee->id }}"></td>
                <td width="20%"><input type="text" value="{{ $employee->name }}" disabled
                                       class="form-control"></td>
                <td><input type="text" value="{{ $employee->mobile }}" disabled
                           class="form-control"></td>
                <td><input type="number" min="0" step="0.01" name="employee[{{ $employee->id }}][wage]"
                           value="{{ $employee->designation->salary }}"

                           class="form-control wage"></td>
                <td><input class="form-control pay_now" type="number" min="0" step="0.01"
                           name="employee[{{ $employee->id }}][pay_now]" value="{{ $employee->designation->salary }}"></td>

                <input type="hidden" name="employee[{{ $employee->id }}][workspace_id]" value="{{ $employee->workspace_id }}">
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="8" style="text-align: center;color: #ff0000">No data found.</td>
        </tr>
    @endif
</table>