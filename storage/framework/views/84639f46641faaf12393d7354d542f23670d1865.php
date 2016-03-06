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

<div class="form-group<?php echo e($errors->has('component_id') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('component_id', 'Component', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::select('component_id', $components, null, ['class' => 'form-control component_id', 'placeholder' => 'Select'])); ?>

        <?php if ($errors->has('component_id')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('component_id')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group module_div<?php echo e($errors->has('module_id') ? ' has-error' : ''); ?>"
     style="display: <?php echo e(isset($task) ? 'show' : 'none'); ?>;">
    <?php echo e(Form::label('module_id', 'Module', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::select('module_id', $modules, null, ['class' => 'form-control module_id', 'placeholder' => 'Select'])); ?>

        <?php if ($errors->has('module_id')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('module_id')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('route') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('route', 'Route', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('route', null, ['class' => 'form-control'])); ?>

        <?php if ($errors->has('route')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('route')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('icon') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('icon', 'Icon', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::text('icon', null, ['class' => 'form-control'])); ?>

        <?php if ($errors->has('icon')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('icon')); ?></strong>
            </span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group<?php echo e($errors->has('description') ? ' has-error' : ''); ?>">
    <?php echo e(Form::label('description', 'Description', ['class' => 'col-md-3 control-label'])); ?>

    <div class="col-md-7">
        <?php echo e(Form::textarea('description', null, ['class' => 'form-control', 'rows' => '3'])); ?>

        <?php if ($errors->has('body')): ?>
            <span class="help-block">
                <strong><?php echo e($errors->first('description')); ?></strong>
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

<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
            <?php echo e(Form::submit($submitText, ['class' => 'btn green'])); ?>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $(document).on("change", "#component_id", function () {
            $(".module_div").show();
            $(".module_id").val("");
            var component_id = $(this).val();

            if (component_id > 0) {
                $.ajax({
                    url: '<?php echo e(route('ajax.module_select')); ?>',
                    type: 'POST',
                    dataType: "JSON",
                    data: {component_id: component_id},
                    success: function (data, status) {
                        $('#module_id').html('<option value="">' + 'Select' + '</option>');
                        $.each(data, function (key, element) {
                            $('#module_id').append("<option value='" + key + "'>" + element + "</option>");
                        });
                    },
                    error: function (xhr, desc, err) {
                        console.log("error");
                    }
                });
            }
            else {
                $(".module_div").hide();
            }
        });
    });

</script>