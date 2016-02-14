<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Daily Production Registers
                    </div>
                    <div>
                        <a style="margin: 12px; padding: 5px;" class="label label-success pull-right" href="<?php echo e(url('/productionRegisters/create')); ?>">New</a>
                    </div>
                </div>

                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        Date
                                    </th>
                                    <th>
                                        Product
                                    </th>
                                    <th>
                                        production
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
                            <?php if(sizeof($productionRegisters)>0): ?>
                                <?php foreach($productionRegisters as $productionRegister): ?>
                                    <tr>
                                        <td>
                                            <?php echo e($productionRegister->date); ?>

                                        </td>
                                        <td>
                                            <?php echo e($productionRegister->product->title); ?>

                                        </td>
                                        <td>
                                            <?php echo e($productionRegister->production); ?>

                                        </td>
                                        <td>
                                            <?php echo e($status[$productionRegister->status]); ?>

                                        </td>
                                        <td>
                                            <a class="label label-danger" href="<?php echo e(url('/productionRegisters/'.$productionRegister->id.'/edit' )); ?>">Edit</a>
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
                    <div class="pagination"> <?php echo e($productionRegisters->links()); ?> </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>