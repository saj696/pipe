<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet box yellow">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-coffee"></i>Articles
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
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($articles as $article): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(url('/articles', $article->id )); ?>"><?php echo e($article->title); ?></a>
                                </td>
                                <td>
                                    <?php echo e(str_limit($article->body, 50)); ?>

                                </td>
                                <td>
                                    <?php echo e($article->published_at); ?>

                                </td>
                                <td>
                                    <a class="label label-danger" href="<?php echo e(url('/articles/'.$article->id.'/edit' )); ?>">Edit</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination"> <?php echo e($articles->links()); ?> </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>