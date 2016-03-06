<?php echo csrf_field(); ?>


<div class="form-group<?php echo e($errors->has('name_en') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('name_en', 'Name EN', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('name_en', null, ['class' => 'form-control'])); ?>

        <?php if ($errors->has('name_en')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('name_en')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('name_bn') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('name_bn', 'Name BN', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('name_bn', null, ['class' => 'form-control'])); ?>

        <?php if ($errors->has('name_bn')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('name_bn')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('ordering') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('ordering', 'Order', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('ordering', null, ['class' => 'form-control'])); ?>

        <?php if ($errors->has('ordering')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('ordering')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('status') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('status', 'Status', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::select('status', ['1' => 'Active', '0' => 'Inactive'], null, ['class' => 'form-control', 'placeholder' => 'Select'])); ?>

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