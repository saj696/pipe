<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>User Group Roles
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <?php /*<?php echo e(trans('user.group_name')); ?>*/ ?>
                                        Group Name
                                    </th>
                                    <th>
                                        Total Component
                                    </th>
                                    <th>
                                        Total Module
                                    </th>
                                    <th>
                                        Total Task
                                    </th>
                                    <th>
                                        Last Edit
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php if(sizeof($groups)>0): ?>
                            <?php foreach($groups as $group): ?>
                                <?php $detail = App\Helpers\UserHelper::getUserGroupRoleDetail($group->id); ?>
                            <tr>
                                <td>
                                    <?php echo e($group->name_en); ?>

                                </td>
                                <td>
                                    <?php echo e($detail->total_component); ?>

                                </td>
                                <td>
                                    <?php echo e($detail->total_module); ?>

                                </td>
                                <td>
                                    <?php echo e($detail->total_task); ?>

                                </td>
                                <td>
                                    <?php echo e(isset($detail->last_update_date)?date('Y-m-d',$detail->last_update_date):'Not Done'); ?>

                                </td>
                                <td>
                                    <a class="label label-danger" href="<?php echo e(url('/roles/'.$group->id.'/edit' )); ?>">Edit</a>
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
                    <div class="pagination"> <?php echo e($groups->links()); ?> </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>