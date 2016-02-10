<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Workspaces
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="<?php echo e(url('/workspaces/create' )); ?>">New</a>
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
                                        Type
                                    </th>
                                    <th>
                                        Parent
                                    </th>
                                    <th>
                                        Location
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
                            <?php if(sizeof($workspaces)>0): ?>
                                <?php foreach($workspaces as $workspace): ?>
                                    <tr>
                                        <td>
                                            <?php echo e($workspace->name); ?>

                                        </td>
                                        <td>
                                            <?php echo e($workspace_types[$workspace->type]); ?>

                                        </td>
                                        <td>
                                            <?php echo e(isset($workspace->parentInfo->name)?$workspace->parentInfo->name:'No Parent'); ?>

                                        </td>
                                        <td>
                                            <?php echo e($workspace->location); ?>

                                        </td>
                                        <td>
                                            <?php echo e($status[$workspace->status]); ?>

                                        </td>
                                        <td>
                                            <a class="label label-danger" href="<?php echo e(url('/workspaces/'.$workspace->id.'/edit' )); ?>">Edit</a>
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
                    <div class="pagination"> <?php echo e($workspaces->links()); ?> </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>