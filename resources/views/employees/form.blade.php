{!! csrf_field() !!}

<div class="form-group">
    {{ Form::label('name', 'Name', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('name') ? ' has-error' : '' }}">
        {{ Form::text('name', null,['class'=>'form-control']) }}
        @if ($errors->has('name'))
            <span class="help-block">
                <strong>{{ $errors->first('name') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('mobile', 'Mobile', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('mobile') ? ' has-error' : '' }}">
        {{ Form::text('mobile', null,['class'=>'form-control']) }}
        @if ($errors->has('mobile'))
            <span class="help-block">
                <strong>{{ $errors->first('mobile') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('email', 'Email', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('email') ? ' has-error' : '' }}">
        {{ Form::text('email', null,['class'=>'form-control']) }}
        @if ($errors->has('email'))
            <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('dob', 'Date Of Birth', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('dob') ? ' has-error' : '' }}">
        {{ Form::text('dob', null,['class'=>'form-control col-md-3', 'id'=>'dob']) }}
        @if ($errors->has('dob'))
            <span class="help-block">
                <strong>{{ $errors->first('dob') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('joining_date', 'Joining Date', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('joining_date') ? ' has-error' : '' }}">
        {{ Form::text('joining_date', null,['class'=>'form-control col-md-3', 'id'=>'date']) }}
        @if ($errors->has('joining_date'))
            <span class="help-block">
                <strong>{{ $errors->first('joining_date') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('designation_id', 'Designation', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('designation_id') ? ' has-error' : '' }}">
        {{ Form::select('designation_id', $designations, null,['class'=>'form-control', 'placeholder'=>'Select']) }}
        @if ($errors->has('designation_id'))
            <span class="help-block">
                <strong>{{ $errors->first('designation_id') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('workspace_id', 'Workspace', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('workspace_id') ? ' has-error' : '' }}">
        {{ Form::select('workspace_id', $workspaces, null,['class'=>'form-control', 'placeholder'=>'Select','required']) }}
        @if ($errors->has('workspace_id'))
            <span class="help-block">
                <strong>{{ $errors->first('workspace_id') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('employee_type', 'Employee Type', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('employee_type') ? ' has-error' : '' }}">
        {{ Form::select('employee_type', array_flip(Config::get('common.employee_type')), null,['class'=>'form-control', 'placeholder'=>'Select','required']) }}
        @if ($errors->has('employee_type'))
            <span class="help-block">
                <strong>{{ $errors->first('employee_type') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('present_address', 'Present Address', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('present_address') ? ' has-error' : '' }}">
        {{ Form::textarea('present_address', null,['class'=>'form-control col-md-3', 'rows'=>3]) }}
        @if ($errors->has('present_address'))
            <span class="help-block">
                <strong>{{ $errors->first('present_address') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('permanent_address', 'Permanent Address', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('permanent_address') ? ' has-error' : '' }}">
        {{ Form::textarea('permanent_address', null,['class'=>'form-control col-md-3', 'rows'=>3]) }}
        @if ($errors->has('permanent_address'))
            <span class="help-block">
                <strong>{{ $errors->first('permanent_address') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group">
    {{ Form::label('photo', 'Photo', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-3{{ $errors->has('photo') ? ' has-error' : '' }}">
        {{ Form::file('photo', null,['class'=>'form-control']) }}
        @if ($errors->has('photo'))
            <span class="help-block">
                <strong>{{ $errors->first('photo') }}</strong>
            </span>
        @endif
    </div>
    <div class="col-md-2">
        @if(isset($employee->photo))
            <img width="80" height="80" src="{{URL::to('/public')}}/image/employee/{{ $employee->photo }}"
                 alt="picture">
        @else
            <img width="80" height="80" src="{{URL::to('/public')}}/image/employee/no_image.jpg" alt="picture">
        @endif
    </div>
</div>

@if(!isset($employee))
    <div class="form-group">
        {{ Form::label('balance', 'Balance', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7{{ $errors->has('balance') ? ' has-error' : '' }}">
            {{ Form::text('balance', null,['class'=>'form-control col-md-3 quantity']) }}
            @if ($errors->has('balance'))
                <span class="help-block">
                <strong>{{ $errors->first('balance') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('due', 'Due', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7{{ $errors->has('due') ? ' has-error' : '' }}">
            {{ Form::text('due', null,['class'=>'form-control col-md-3 quantity']) }}
            @if ($errors->has('due'))
                <span class="help-block">
                <strong>{{ $errors->first('due') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('as_user', 'Create As User', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-1">
            {{ Form::checkbox('as_user', 1, null, ['class' => 'form-control col-md-1 as_user']) }}
        </div>
    </div>

    <div class="form-group for_user"
         style="display: {{ $errors->has('username') ? ' show' : 'none' }};">
        {{ Form::label('username', 'Username', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7{{ $errors->has('username') ? ' has-error' : '' }}">
            {{ Form::text('username', null,['class'=>'form-control username']) }}
            @if ($errors->has('username'))
                <span class="help-block">
                <strong>{{ $errors->first('username') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group for_user"
         style="display: {{ $errors->has('password') ? ' show' : 'none' }};">
        {{ Form::label('password', 'Password', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7{{ $errors->has('password') ? ' has-error' : '' }}">
            {{ Form::password('password', ['class'=>'form-control password']) }}
            @if ($errors->has('password'))
                <span class="help-block">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group for_user"
         style="display: {{ $errors->has('user_group_id') ? ' show' : 'none' }};">
        {{ Form::label('user_group_id', 'User Group', ['class'=>'col-md-3 control-label']) }}
        <div class="col-md-7{{ $errors->has('user_group_id') ? ' has-error' : '' }}">
            {{ Form::select('user_group_id', $userGroups, null,['class'=>'form-control user_group_id', 'placeholder'=>'Select']) }}
            @if ($errors->has('user_group_id'))
                <span class="help-block">
                <strong>{{ $errors->first('user_group_id') }}</strong>
            </span>
            @endif
        </div>
    </div>
@endif

<div class="form-group">
    {{ Form::label('status', 'Status', ['class'=>'col-md-3 control-label']) }}
    <div class="col-md-7{{ $errors->has('status') ? ' has-error' : '' }}">
        {{ Form::select('status', Config::get('common.status'), 1,['class'=>'form-control', 'placeholder'=>'Select']) }}
        @if ($errors->has('status'))
            <span class="help-block">
                <strong>{{ $errors->first('status') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-actions">
    <div class="row">
        <div class="text-center col-md-12">
            {{ Form::submit($submitText, ['class'=>'btn btn-circle green']) }}
        </div>
    </div>
</div>

<script type="text/javascript">

    $(function () {
        $("#date").datepicker();
        $("#dob").datepicker();
    });

    jQuery(document).ready(function () {
        $(document).on("keyup", ".quantity", function () {
            this.value = this.value.replace(/[^0-9\.]/g, '');
        });

        $(document).on("click", ".as_user", function () {
            if ($('.as_user').prop('checked')) {
                $('.for_user').show();
            }
            else {
                $('.for_user').hide();
                $('.user_group_id').val('');
                $('.username').val('');
                $('.password').val('');
            }
        });
    });
</script>