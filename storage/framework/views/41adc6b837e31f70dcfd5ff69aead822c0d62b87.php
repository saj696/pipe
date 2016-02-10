<?php $__env->startSection('content'); ?>

    <div class="portlet box green ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i> New User
            </div>
            <div>
                <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="<?php echo e(url('/users' )); ?>">Back</a>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-horizontal" role="form">
                <div class="form-body">
                    <?php echo e(Form::open(['url'=>'users'])); ?>

                    <?php echo $__env->make('users.form', ['submitText'=>'Add'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <?php echo e(Form::close()); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>