<?php echo csrf_field(); ?>


<div class="form-group<?php echo e($errors->has('title') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('title', 'Title', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('title', null, ['class' => 'form-control'])); ?>

        <?php if ($errors->has('title')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('title')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('body') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('body', 'Body', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::textarea('body', null, ['class' => 'form-control', 'rows' => '3'])); ?>

        <?php if ($errors->has('body')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('body')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('published_at') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('published_at', 'Published On', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::input('date', 'published_at', date('Y-m-d'), ['class' => 'form-control'])); ?>

        <?php if ($errors->has('published_at')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('published_at')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group">
    <?php echo e(Form::label('tag_list', 'Tags', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::select('tag_list[]', $tags, null, ['class' => 'form-control', 'multiple'])); ?>

    </div>
</div>

<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
            <?php echo e(Form::submit($submitText, ['class' => 'btn green'])); ?>

        </div>
    </div>
</div>