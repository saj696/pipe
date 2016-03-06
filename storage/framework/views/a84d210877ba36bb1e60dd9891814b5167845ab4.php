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

<div class="form-group<?php echo e($errors->has('parent') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('parent', 'Parent', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::select('parent', $parents, null, ['class' => 'form-control', 'id' => 'parent', 'placeholder' => 'Select'])); ?>

        <?php if ($errors->has('parent')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('parent')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('location') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('location', 'Location', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('location', null, ['class' => 'form-control'])); ?>

        <?php if ($errors->has('location')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('location')); ?></strong>
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
    $(document).ready(function () {
        $(document).on("change", "#parent", function () {
            var parent_id = $(this).val();
            var type_id = $('#type').val();

            if (parent_id > 0) {
                $.ajax({
                    url: '<?php echo e(route('ajax.parent_select')); ?>',
                    type: 'POST',
                    dataType: "JSON",
                    data: {parent_id: parent_id, type_id: type_id},
                    success: function (data, status) {
                        if (type_id == 2 && data == 1) {
                            $("#parent").val('');
                            alert('You can not add a delivery center under a showroom!');
                        }
                    },
                    error: function (xhr, desc, err) {
                        console.log("error");
                    }
                });
            }
        });
    });
</script>