<?php echo csrf_field(); ?>


<div class="form-group<?php echo e($errors->has('name_en') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('name_en', 'Name EN', ['class'=>'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('name_en', null,['class'=>'form-control'])); ?>

        <?php if($errors->has('name_en')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('name_en')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('name_bn') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('name_bn', 'Name BN', ['class'=>'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('name_bn', null,['class'=>'form-control'])); ?>

        <?php if($errors->has('name_bn')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('name_bn')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group">
    <?php echo e(Form::label('component_id', 'Component', ['class'=>'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::select('component_id', $components, null,['class'=>'form-control'])); ?>

    </div>
</div>

<div class="form-group<?php echo e($errors->has('icon') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('icon', 'Icon', ['class'=>'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('icon', null,['class'=>'form-control'])); ?>

        <?php if($errors->has('icon')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('icon')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('description') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('description', 'Description', ['class'=>'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::textarea('description', null,['class'=>'form-control', 'rows'=>'3'])); ?>

        <?php if($errors->has('body')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('description')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('ordering') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('ordering', 'Order', ['class'=>'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('ordering', null,['class'=>'form-control'])); ?>

        <?php if($errors->has('ordering')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('ordering')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
        <?php echo e(Form::submit($submitText, ['class'=>'btn green'])); ?>

        </div>
    </div>
</div>