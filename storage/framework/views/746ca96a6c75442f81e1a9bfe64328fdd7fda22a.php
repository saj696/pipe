<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Users
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right"
                           href="<?php echo e(url('/users/create')); ?>">New</a>
                    </div>
                </div>
                <?php
                //                echo '<pre>';
                //                print_r(Config::get('common.name'));
                //                echo '</pre>';
                ?>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>
                                    Username
                                </th>
                                <th>
                                    email
                                </th>
                                <th>
                                    Name
                                </th>
                                <th>
                                    User Group
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
                            <?php if (sizeof($users) > 0): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <?php echo e($user->username); ?>

                                        </td>
                                        <td>
                                            <?php echo e($user->email); ?>

                                        </td>
                                        <td>
                                            <?php echo e($user->name_en); ?>

                                        </td>
                                        <td>
                                            <?php echo e($user->userGroup->name_en); ?>

                                        </td>
                                        <td>
                                            <?php echo e($user->status == 1 ? 'Active' : 'Inactive'); ?>

                                        </td>
                                        <td>
                                            <a class="label label-danger"
                                               href="<?php echo e(url('/users/' . $user->id . '/edit')); ?>">Edit</a>
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
                    <div class="pagination"> <?php echo e($users->links()); ?> </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>