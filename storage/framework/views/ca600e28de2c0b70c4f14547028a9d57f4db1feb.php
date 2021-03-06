<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Module Detail
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="<?php echo e(url('/modules')); ?>">Back</a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>
                                    Name
                                </th>
                                <th>
                                    Component
                                </th>
                                <th>
                                    Description
                                </th>
                                <th>
                                    Icon
                                </th>
                                <th>
                                    Order
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <?php echo e($module->name_en); ?>

                                </td>
                                <td>
                                    <p><?php echo e($module->component->name_en); ?></p>
                                </td>
                                <td>
                                    <p><?php echo e($module->description); ?></p>
                                </td>
                                <td>
                                    <?php echo e($module->icon); ?>

                                </td>
                                <td>
                                    <?php echo e($module->ordering); ?>

                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END BORDERED TABLE PORTLET-->
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>