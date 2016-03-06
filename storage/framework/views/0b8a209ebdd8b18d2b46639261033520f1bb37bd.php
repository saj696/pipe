<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Designations
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="<?php echo e(url('/designations/create')); ?>">New</a>
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
                                    Salary
                                </th>
                                <th>
                                    Hourly Rate
                                </th>
                                <th>
                                    Status
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (sizeof($designations) > 0): ?>
                                <?php foreach ($designations as $designation): ?>
                                    <tr>
                                        <td>
                                            <?php echo e($designation->name); ?>

                                        </td>
                                        <td>
                                            <?php echo e($designation->salary); ?>

                                        </td>
                                        <td>
                                            <?php echo e($designation->hourly_rate > 0 ? $designation->hourly_rate : 'N/A'); ?>

                                        </td>
                                        <td>
                                            <?php echo e($status[$designation->status]); ?>

                                        </td>
                                        <td>
                                            <a class="label label-danger"
                                               href="<?php echo e(url('/designations/' . $designation->id . '/edit')); ?>">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center danger">No Data Found</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination"> <?php echo e($designations->links()); ?> </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>