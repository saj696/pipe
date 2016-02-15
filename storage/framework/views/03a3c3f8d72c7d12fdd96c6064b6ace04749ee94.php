<?php $__env->startSection('content'); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i> Edit: <?php echo e($materials[$UsageRegister->material_id]); ?>

            </div>
            <div class="tools">
                <a href="" class="collapse">
                </a>
                <a href="#portlet-config" data-toggle="modal" class="config">
                </a>
                <a href="" class="reload">
                </a>
                <a href="" class="remove">
                </a>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    <?php echo e(Form::model($UsageRegister, ['method'=>'PATCH','action'=>['Register\UsageRegistersController@update', $UsageRegister->id]])); ?>

                    <?php echo $__env->make('usageRegisters.editForm', ['submitText'=>'Update'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <?php echo e(Form::close()); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>