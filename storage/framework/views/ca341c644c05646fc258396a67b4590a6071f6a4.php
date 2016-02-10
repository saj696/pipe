<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Article Detail
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse">
                        </a>
                        <a href="#portlet-config" data-toggle="modal" class="config">
                        </a>
                        <a href="javascript:;" class="reload">
                        </a>
                        <a href="javascript:;" class="remove">
                        </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>
                                    Title
                                </th>
                                <th>
                                    Body
                                </th>
                                <th>
                                    Published On
                                </th>
                                <th>
                                    Tags
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <?php echo e($article->title); ?>

                                    </td>
                                    <td>
                                        <p><?php echo e($article->body); ?></p>
                                    </td>
                                    <td>
                                        <?php echo e($article->published_at); ?>

                                    </td>
                                    <td>
                                        <?php if ( ! ($article->tags->isEmpty())): ?>
                                            <?php foreach($article->tags as $tag): ?>
                                                <p><?php echo e($tag->name); ?></p>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
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