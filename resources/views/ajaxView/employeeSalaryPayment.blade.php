@if(!empty($salary))
    <input type="hidden" name="employee_id" value="{{ $salary->employee_id }}">
    <input type="hidden" name="workspace_id" value="{{ $employee->workspace_id }}">
    <input type="hidden" name="salary_id" value="{{ $salary->id }}">
    <div class="form-group">
        {{ Form::label('', 'Name', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7">
            {{ Form::text('', $employee->name,['class'=>'form-control','disabled']) }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('', 'Designation', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7">
            {{ Form::text('', $employee->designation->name,['class'=>'form-control','disabled']) }}
        </div>
    </div>
    <div class="form-group">
        {{ Form::label('', 'Workspace', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7">
            {{ Form::text('', $employee->workspace->name,['class'=>'form-control','disabled']) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('', 'Payable Amount', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7">
            {{ Form::text('', $salary->net_due+$salary->over_time_due+$salary->bonus_due,['class'=>'form-control','disabled']) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('pay_now', 'Pay Now', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7">
            {{ Form::number('pay_now', null,['class'=>'form-control','min'=>0,'step'=>0.01,'max'=>$salary->net_due+$salary->over_time_due+$salary->bonus_due]) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('voucher_no', 'Voucher No.', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7">
            {{ Form::text('voucher_no', null,['class'=>'form-control']) }}
        </div>
    </div>

    @if($salary->net_due > 0)
        <div class="form-group">
            {{ Form::label('net_salary', 'Net Salary', ['class'=>'col-md-offset-3 col-md-2']) }}
            {{ Form::label('net_paid', 'Net Salary Paid', ['class'=>'col-md-2']) }}
            {{ Form::label('net_pay', 'Net Pay Now', ['class'=>'col-md-2']) }}
        </div>
        <div class="form-group">
            <div class="col-md-offset-3 col-md-2">
                {{ Form::text('net_salary', $salary->net,['class'=>'form-control','disabled']) }}
            </div>
            <div class="col-md-2">
                {{ Form::text('net_paid', $salary->net_paid,['class'=>'form-control','disabled']) }}
            </div>
            <div class="col-md-2">
                {{ Form::number('net_pay', null,['class'=>'form-control','min'=>0,'step'=>0.01,'max'=>$salary->net_due]) }}
            </div>
        </div>
    @endif

    @if($salary->bonus > 0)
        <div class="form-group">
            {{ Form::label('bonus', 'Bonus', ['class'=>'col-md-offset-3 col-md-2']) }}
            {{ Form::label('bonus_paid', 'Bonus Paid', ['class'=>'col-md-2']) }}
            {{ Form::label('bonus_pay', 'Bonus Pay Now', ['class'=>'col-md-2']) }}
        </div>
        <div class="form-group">
            <div class="col-md-offset-3 col-md-2">
                {{ Form::text('bonus', $salary->bonus,['class'=>'form-control','disabled']) }}
            </div>
            <div class="col-md-2">
                {{ Form::text('bonus_paid', $salary->bonus_paid,['class'=>'form-control','disabled']) }}
            </div>
            <div class="col-md-2">
                {{ Form::number('bonus_pay', null,['class'=>'form-control','min'=>0,'step'=>0.01,'max'=>$salary->bonus_due]) }}
            </div>
        </div>
    @endif

    @if($salary->over_time_amount > 0)
        <div class="form-group">
            {{ Form::label('over_time_amount', 'Overtime Amount', ['class'=>'col-md-offset-3 col-md-2']) }}
            {{ Form::label('over_time_paid', 'Overtime Paid', ['class'=>'col-md-2']) }}
            {{ Form::label('over_time_pay', 'Overtime Pay Now', ['class'=>'col-md-2']) }}
        </div>
        <div class="form-group">
            <div class="col-md-offset-3 col-md-2">
                {{ Form::text('over_time_amount', $salary->over_time_amount,['class'=>'form-control','disabled']) }}
            </div>
            <div class="col-md-2">
                {{ Form::text('over_time_paid', $salary->over_time_paid,['class'=>'form-control','disabled']) }}
            </div>
            <div class="col-md-2">
                {{ Form::number('over_time_pay', null,['class'=>'form-control','min'=>0,'step'=>0.01,'max'=>$salary->over_time_due]) }}
            </div>
        </div>
    @endif
@else
    <div class="has-error text-center">
        <span class="help-block">
            <strong> No salary found!.</strong>
        </span>
    </div>

@endif



