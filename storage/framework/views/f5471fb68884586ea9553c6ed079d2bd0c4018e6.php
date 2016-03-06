<?php $__env->startSection('content'); ?>

    <div class="content">
        <form class="login-form" method="POST" action="<?php echo e(url('/login')); ?>">
            <?php echo csrf_field(); ?>

            <h3 class="form-title">Sign In</h3>
            <div class="alert alert-danger display-hide">
                <button class="close" data-close="alert"></button>
			<span>
			Enter any username and password. </span>
            </div>
            <div class="form-group<?php echo e($errors->has('username') ? ' has-error' : ''); ?>">
                <label class="control-label visible-ie8 visible-ie9">Username</label>
                <input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off"
                       placeholder="Username" name="username" value="<?php echo e(old('username')); ?>"/>
                <?php if ($errors->has('name')): ?>
                    <span class="help-block">
                        <strong><?php echo e($errors->first('username')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>
            <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                <label class="control-label visible-ie8 visible-ie9">Password</label>
                <input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off"
                       placeholder="Password" name="password"/>
                <?php if ($errors->has('password')): ?>
                    <span class="help-block">
                        <strong><?php echo e($errors->first('password')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>
            <div class="form-actions">
                <input type="submit" class="btn btn-success uppercase" value="Submit"/>
                <label class="rememberme check">
                    <input type="checkbox" name="remember" value="1"/>Remember </label>
                <a href="javascript:;" id="forget-password" class="forget-password">Forgot Password?</a>
            </div>
        </form>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.login', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>