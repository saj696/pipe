<?php echo csrf_field(); ?>


<div class="form-group<?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('name', 'Name', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('name', null, ['class' => 'form-control'])); ?>

        <?php if ($errors->has('name')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('name')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('salary') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('salary', 'Salary', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('salary', null, ['class' => 'form-control quantity'])); ?>

        <?php if ($errors->has('salary')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('salary')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('hourly_rate') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('hourly_rate', 'Hourly Rate', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('hourly_rate', null, ['class' => 'form-control quantity'])); ?>

        <?php if ($errors->has('hourly_rate')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('hourly_rate')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('status') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('status', 'Status', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::select('status', Config::get('common.status'), 1, ['class' => 'form-control', 'placeholder' => 'Select'])); ?>

        <?php if ($errors->has('status')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('status')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
            <?php echo e(Form::submit($submitText, ['class' => 'btn green'])); ?>

        </div>
    </div>
</div>

<script type="text/javascript">

    jQuery(document).ready(function () {
        $(document).on("keyup", ".quantity", function () {
            this.value = this.value.replace(/[^0-9\.]/g, '');
        });
    });
</script>