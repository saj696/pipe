<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Employees
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="<?php echo e(url('/employees/create' )); ?>">New</a>
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
                                        Designation
                                    </th>
                                    <th>
                                        Mobile
                                    </th>
                                    <th>
                                        Email
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
                            <?php if(sizeof($employees)>0): ?>
                            <?php foreach($employees as $employee): ?>
                            <tr>
                                <td>
                                    <?php echo e($employee->name); ?>

                                </td>
                                <td>
                                    <?php echo e($employee->designation->name); ?>

                                </td>
                                <td>
                                    <?php echo e($employee->mobile); ?>

                                </td>
                                <td>
                                    <?php echo e($employee->email); ?>

                                </td>
                                <td>
                                    <?php echo e($status[$employee->status]); ?>

                                </td>
                                <td>
                                    <a class="label label-danger" href="<?php echo e(url('/employees/'.$employee->id.'/edit' )); ?>">Edit</a>
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
                    <div class="pagination"> <?php echo e($employees->links()); ?> </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>