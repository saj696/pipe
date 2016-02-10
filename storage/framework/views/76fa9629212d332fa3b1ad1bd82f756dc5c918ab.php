<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Components
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="<?php echo e(url('/components/create' )); ?>">New</a>
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
                            <?php if(sizeof($components)>0): ?>
                            <?php foreach($components as $component): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(url('/components', $component->id )); ?>"><?php echo e($component->name_en); ?></a>
                                </td>
                                <td>
                                    <?php echo e(str_limit($component->description, 50)); ?>

                                </td>
                                <td>
                                    <?php echo e($component->icon); ?>

                                </td>
                                <td>
                                    <?php echo e($component->ordering); ?>

                                </td>
                                <td>
                                    <a class="label label-danger" href="<?php echo e(url('/components/'.$component->id.'/edit' )); ?>">Edit</a>
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
                    <div class="pagination"> <?php echo e($components->links()); ?> </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>