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

<div class="form-group<?php echo e($errors->has('type') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('type', 'Type', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::select('type', $types, null, ['class' => 'form-control', 'id' => 'type', 'placeholder' => 'Select'])); ?>

        <?php if ($errors->has('type')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('type')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('status') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('status', 'Status', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::select('status', Config::get('common.status'), null, ['class' => 'form-control', 'placeholder' => 'Select'])); ?>

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