<?php echo csrf_field(); ?>


<div class="form-group<?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('name', 'Name', ['class'=>'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('name', null,['class'=>'form-control'])); ?>

        <?php if($errors->has('name')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('name')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('mobile') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('mobile', 'Mobile', ['class'=>'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('mobile', null,['class'=>'form-control'])); ?>

        <?php if($errors->has('mobile')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('mobile')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('email', 'Email', ['class'=>'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('email', null,['class'=>'form-control'])); ?>

        <?php if($errors->has('email')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('email')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('dob') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('dob', 'Date Of Birth', ['class'=>'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('dob', null,['class'=>'form-control col-md-3', 'id'=>'dob'])); ?>

        <?php if($errors->has('dob')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('dob')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('joining_date') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('joining_date', 'Joining Date', ['class'=>'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('joining_date', null,['class'=>'form-control col-md-3', 'id'=>'date'])); ?>

        <?php if($errors->has('joining_date')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('joining_date')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('designation') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('designation', 'Designation', ['class'=>'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::select('designation', $designations, null,['class'=>'form-control', 'placeholder'=>'Select'])); ?>

        <?php if($errors->has('designation')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('designation')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('present_address') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('present_address', 'Present Address', ['class'=>'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::textarea('present_address', null,['class'=>'form-control col-md-3', 'rows'=>3])); ?>

        <?php if($errors->has('present_address')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('present_address')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('permanent_address') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('permanent_address', 'Permanent Address', ['class'=>'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::textarea('permanent_address', null,['class'=>'form-control col-md-3', 'rows'=>3])); ?>

        <?php if($errors->has('permanent_address')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('permanent_address')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('status') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('status', 'Status', ['class'=>'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::select('status', Config::get('common.status'), 1,['class'=>'form-control', 'placeholder'=>'Select'])); ?>

        <?php if($errors->has('status')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('status')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-actions">
    <div class="row">
        <div class="text-center col-md-12">
            <?php echo e(Form::submit($submitText, ['class'=>'btn green'])); ?>

        </div>
    </div>
</div>

<script type="text/javascript">

    $(function() {
        $( "#date" ).datepicker();
        $( "#dob" ).datepicker();
    });

    jQuery(document).ready(function()
    {
        $(document).on("keyup", ".quantity", function()
        {
            this.value = this.value.replace(/[^0-9\.]/g,'');
        });
    });
</script>