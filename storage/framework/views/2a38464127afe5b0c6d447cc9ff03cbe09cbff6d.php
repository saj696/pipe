<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Modules
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="<?php echo e(url('/modules/create' )); ?>">New</a>
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
                                    <th>
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(sizeof($modules)>0): ?>
                            <?php foreach($modules as $module): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(url('/modules', $module->id )); ?>"><?php echo e($module->name_en); ?></a>
                                </td>
                                <td>
                                    <?php echo e($module->component->name_en); ?>

                                </td>
                                <td>
                                    <?php echo e(str_limit($module->description, 50)); ?>

                                </td>
                                <td>
                                    <?php echo e($module->icon); ?>

                                </td>
                                <td>
                                    <?php echo e($module->ordering); ?>

                                </td>
                                <td>
                                    <a class="label label-danger" href="<?php echo e(url('/modules/'.$module->id.'/edit' )); ?>">Edit</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center danger">No Data Found</td>
                            </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination"> <?php echo e($modules->links()); ?> </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>