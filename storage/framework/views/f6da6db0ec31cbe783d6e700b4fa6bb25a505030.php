<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Tasks
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="<?php echo e(url('/tasks/create' )); ?>">New</a>
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
                                        Module
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
                            <?php if(sizeof($tasks)>0): ?>
                            <?php foreach($tasks as $task): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(url('/tasks', $task->id )); ?>"><?php echo e($task->name_en); ?></a>
                                </td>
                                <td>
                                    <?php echo e($task->component->name_en); ?>

                                </td>
                                <td>
                                    <?php echo e($task->module->name_en); ?>

                                </td>
                                <td>
                                    <?php echo e(str_limit($task->description, 50)); ?>

                                </td>
                                <td>
                                    <?php echo e($task->icon); ?>

                                </td>
                                <td>
                                    <?php echo e($task->ordering); ?>

                                </td>
                                <td>
                                    <a class="label label-danger" href="<?php echo e(url('/tasks/'.$task->id.'/edit' )); ?>">Edit</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center danger">No Data Found</td>
                            </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination"> <?php echo e($tasks->links()); ?> </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>