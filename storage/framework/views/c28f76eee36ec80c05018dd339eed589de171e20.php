<?php echo csrf_field(); ?>


<div class="form-group<?php echo e($errors->has('date') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('date', 'Date', ['class' => 'col-md-4 control-label'])); ?>

    <div class="col-md-4">
        <?php echo e(Form::text('date', null, ['class' => 'form-control col-md-2'])); ?>

        <?php if ($errors->has('name')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('date')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('material_id') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('material_id', 'Material', ['class' => 'col-md-4 control-label'])); ?>

    <div class="col-md-4">
        <?php echo e(Form::select('material_id', $materials, null, ['class' => 'form-control', 'placeholder' => 'Select'])); ?>

        <?php if ($errors->has('material_id')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('material_id')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('usage') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('usage', 'usage', ['class' => 'col-md-4 control-label'])); ?>

    <div class="col-md-4">
        <?php echo e(Form::text('usage', null, ['class' => 'form-control col-md-2'])); ?>

        <?php if ($errors->has('usage')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('usage')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('status') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('status', 'Status', ['class' => 'col-md-4 control-label'])); ?>

    <div class="col-md-4">
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
        <div class="text-center col-md-12">
            <?php echo e(Form::submit($submitText, ['class' => 'btn green'])); ?>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $("#date").datepicker();
    });
</script>