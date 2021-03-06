<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>POS | Admin</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <script src="<?php echo e(URL::asset('js/jquery.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('jquery-migrate.min.js')); ?>" type="text/javascript"></script>
    <!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
    <script src="<?php echo e(URL::asset('js/jquery-ui-1.10.3.custom.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/bootstrap.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/bootstrap-hover-dropdown.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/jquery.slimscroll.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/jquery.blockui.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/jquery.cokie.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/jquery.uniform.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/bootstrap-switch.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/jquery-2.1.1.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/jquery-ui.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/jquery-ui.min_full.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/respond.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/excanvas.min.js')); ?>" type="text/javascript"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
    <script src="http://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="<?php echo e(URL::asset('js/jquery.pulsate.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/moment.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/daterangepicker.js')); ?>" type="text/javascript"></script>
    <!-- IMPORTANT! fullcalendar depends on jquery-ui-1.10.3.custom.min.js for drag & drop support -->
    <script src="<?php echo e(URL::asset('js/fullcalendar.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/jquery.easypiechart.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/jquery.sparkline.min.js')); ?>" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="<?php echo e(URL::asset('js/metronic.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/layout.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/quick-sidebar.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/demo.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/index.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/tasks.js')); ?>" type="text/javascript"></script>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet"
          type="text/css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.2.3/css/simple-line-icons.css"
          rel="stylesheet" type="text/css"/>
    <?php /*    <link href="<?php echo e(URL::asset('css/font-awesome.min.css')); ?>" rel="stylesheet" type="text/css"/>*/ ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

    <link href="<?php echo e(URL::asset('css/simple-line-icons.min.css')); ?>" rel="stylesheet" type="text/css"/>

    <link href="<?php echo e(URL::asset('css/bootstrap.min.css')); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo e(URL::asset('css/uniform.default.css')); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo e(URL::asset('css/bootstrap-switch.min.css')); ?>" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
    <link href="<?php echo e(URL::asset('css/daterangepicker-bs3.css')); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo e(URL::asset('css/fullcalendar.min.css')); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo e(URL::asset('css/jqvmap.css')); ?>" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL PLUGIN STYLES -->
    <!-- BEGIN PAGE STYLES -->
    <link href="<?php echo e(URL::asset('css/tasks.css')); ?>" rel="stylesheet" type="text/css"/>
    <!-- END PAGE STYLES -->
    <!-- BEGIN THEME STYLES -->
    <!-- DOC: To use 'rounded corners' style just load 'components-rounded.css' stylesheet instead of 'components.css' in the below style tag -->
    <link href="<?php echo e(URL::asset('css/components.css')); ?>" id="style_components" rel="stylesheet"
          type="text/css"/>
    <link href="<?php echo e(URL::asset('css/plugins.css')); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo e(URL::asset('css/layout.css')); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo e(URL::asset('css/darkblue.css')); ?>" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="<?php echo e(URL::asset('css/custom.css')); ?>" rel="stylesheet" type="text/css"/>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

    <!-- END THEME STYLES -->
    <link rel="shortcut icon" href="favicon.ico"/>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content page-style-square">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="index.html">
                <img src="<?php echo e(URL::asset('public/image/logo.png')); ?>" alt="logo" class="logo-default"/>
            </a>
            <div class="menu-toggler sidebar-toggler hide">
                <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
            </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse"
           data-target=".navbar-collapse">
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">
                <li class="dropdown dropdown-user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                       data-close-others="true">
                        <img alt="" class="img-circle" src="<?php echo e(URL::asset('public/image/jr_small.jpg')); ?>"/>
					<span class="username username-hide-on-mobile">
					<?php if (Auth::check()): ?><?php echo e(Auth::user()->username); ?><?php else: ?><?php echo e('Guest'); ?><?php endif; ?></span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <?php if (Auth::guest()): ?>
                            <li><a href="<?php echo e(url('/login')); ?>">Login</a></li>
                            <li><a href="<?php echo e(url('/register')); ?>">Register</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo e(url('/logout')); ?>"><i class="icon-key"></i></i>Logout</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN SIDEBAR -->
    <?php if (Auth::check()): ?>
        <?php
//        $components = App\Helpers\UserHelper::get_task_module_component('position_left_01');
        $menus = Cache::get('menu');
        ?>
        <div class="page-sidebar-wrapper">
            <div class="page-sidebar navbar-collapse collapse">
                <ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                    <li class="sidebar-toggler-wrapper">
                        <div class="sidebar-toggler">
                        </div>
                    </li>

                    <?php
                    if (is_array($menus) && sizeof($menus) > 0) {
                        foreach ($menus as $key1 => $component) {
                            foreach ($component['modules'] as $key2 => $module) {
                                ?>
                                <li class="<?php echo e(($key2 == 0) ? 'start active open' : ''); ?>">
                                    <a href="javascript:;">
                                        <i class="<?php echo e($module['module_icon']); ?>"></i>
                                        <span class="title"><?php echo e($module['module_name']); ?></span>
                                        <span class="selected"></span>
                                        <span class="arrow open"></span>
                                    </a>
                                    <ul class="sub-menu">
                                        <?php
                                        foreach ($module['tasks'] as $task) {
                                            ?>
                                            <li class="active">
                                                <a href="<?php echo e(url('/' . $task->route)); ?>">
                                                    <i class="<?php echo e($task->task_icon); ?>"></i>
                                                    <?php echo e($task->task_name); ?>

                                                </a>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </li>
                                <?php
                            }
                            ?>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="modal fade" id="portlet-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h4 class="modal-title">Modal title</h4>
                        </div>
                        <div class="modal-body">
                            Widget settings form goes here
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn blue">Save changes</button>
                            <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="page-title">
                Dashboard
            </h3>
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <i class="fa fa-home"></i>
                        <a href="<?php echo e(url('/')); ?>">Home</a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a href="<?php echo e(url('/')); ?>">Dashboard</a>
                    </li>
                </ul>
                <div class="page-toolbar">
                    <div id="dashboard-report-range" class="pull-right tooltips btn btn-fit-height grey-salt"
                         data-placement="top" data-original-title="Change dashboard date range">
                        <i class="icon-calendar"></i>&nbsp;
                        <span class="thin uppercase visible-lg-inline-block">&nbsp;</span>&nbsp;
                        <i class="fa fa-angle-down"></i>
                    </div>
                </div>
            </div>
            <div class="clearfix">
            </div>

            <?php if (Session::has('flash_message')): ?>
                <div
                    class="alert alert-success<?php echo e(Session::has('flash_message_important') ? 'alert-important' : ''); ?>">
                    <?php if (Session::has('flash_message_important')): ?>
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <?php endif; ?>
                    <?php echo e(Session::get('flash_message')); ?>

                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>

        </div>
    </div>
</div>

<script type="text/javascript">
    $('div.alert').not('.alert-important').delay(3000).slideUp(300);
</script>

<div class="page-footer">
    <div class="page-footer-inner">
        2016 &copy; Soft-BD Ltd.
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>


<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>

<![endif]-->

<!-- END PAGE LEVEL SCRIPTS -->
<script>
    jQuery(document).ready(function () {
        Metronic.init(); // init metronic core componets
        Layout.init(); // init layout
        QuickSidebar.init(); // init quick sidebar
        Demo.init(); // init demo features
        Index.init();
        Index.initDashboardDaterange();
        Index.initJQVMAP(); // init index page's custom scripts
        Index.initCalendar(); // init index page's custom scripts
        Index.initCharts(); // init index page's custom scripts
        Index.initChat();
        Index.initMiniCharts();
        Tasks.initDashboardWidget();
    });
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>