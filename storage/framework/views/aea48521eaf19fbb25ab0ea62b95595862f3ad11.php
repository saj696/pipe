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

<div class="form-group<?php echo e($errors->has('product_id') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('product_id', 'Product', ['class' => 'col-md-4 control-label'])); ?>

    <div class="col-md-4">
        <?php echo e(Form::select('product_id', $products, null, ['class' => 'form-control', 'placeholder' => 'Select', 'disabled' => 'disabled'])); ?>

        <?php if ($errors->has('product_id')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('product_id')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('production') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('production', 'production', ['class' => 'col-md-4 control-label'])); ?>

    <div class="col-md-4">
        <?php echo e(Form::text('production', null, ['class' => 'form-control col-md-2'])); ?>

        <?php if ($errors->has('production')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('production')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<?php /*<div class="form-group<?php echo e($errors->has('status') ? ' has-error' : ''); ?>">*/ ?>
<?php /*<?php echo e(Form::label('status', 'Status', ['class'=>'col-md-4 control-label'])); ?>*/ ?>
<?php /*<div class="col-md-4">*/ ?>
<?php /*<?php echo e(Form::select('status', Config::get('common.status'), null,['class'=>'form-control', 'placeholder'=>'Select'])); ?>*/ ?>
<?php /*<?php if($errors->has('status')): ?>*/ ?>
<?php /*<span class="help-block">*/ ?>
<?php /*<strong><?php echo e($errors->first('status')); ?></strong>*/ ?>
<?php /*</span>*/ ?>
<?php /*<?php endif; ?>*/ ?>
<?php /*</div>*/ ?>
<?php /*</div>*/ ?>

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