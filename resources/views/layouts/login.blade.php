<!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>POS</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <script src="{{ URL::asset('js/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('jquery-migrate.min.js') }}" type="text/javascript"></script>
    <!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
    <script src="{{ URL::asset('js/jquery-ui-1.10.3.custom.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/bootstrap-hover-dropdown.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/jquery.blockui.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/jquery.cokie.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/jquery.uniform.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/bootstrap-switch.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/jquery-2.1.1.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/jquery-ui.min_full.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/respond.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/excanvas.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="http://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="{{ URL::asset('js/jquery.pulsate.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/moment.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/daterangepicker.js') }}" type="text/javascript"></script>
    <!-- IMPORTANT! fullcalendar depends on jquery-ui-1.10.3.custom.min.js for drag & drop support -->
    <script src="{{ URL::asset('js/fullcalendar.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/jquery.easypiechart.min.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/jquery.sparkline.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ URL::asset('js/metronic.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/layout.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/quick-sidebar.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/demo.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/index.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/tasks.js') }}" type="text/javascript"></script>
    <script src="{{ URL::asset('js/login.js') }}" type="text/javascript"></script>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.2.3/css/simple-line-icons.css" rel="stylesheet" type="text/css"/>
    {{--    <link href="{{ URL::asset('css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>--}}
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

    <link href="{{ URL::asset('css/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('css/login.css') }}" rel="stylesheet" type="text/css"/>

    <link href="{{ URL::asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('css/uniform.default.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('css/bootstrap-switch.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
    <link href="{{ URL::asset('css/daterangepicker-bs3.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('css/fullcalendar.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('css/jqvmap.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL PLUGIN STYLES -->
    <!-- BEGIN PAGE STYLES -->
    <link href="{{ URL::asset('css/tasks.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END PAGE STYLES -->
    <!-- BEGIN THEME STYLES -->
    <!-- DOC: To use 'rounded corners' style just load 'components-rounded.css' stylesheet instead of 'components.css' in the below style tag -->
    <link href="{{ URL::asset('css/components.css') }}" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('css/plugins.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('css/layout.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('css/darkblue.css') }}" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="{{ URL::asset('css/custom.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END THEME STYLES -->
    <link rel="shortcut icon" href="favicon.ico"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGO -->
<div class="logo">
    <a href="index.html">
        <img src="../../assets/admin/layout/img/logo-big.png" alt=""/>
    </a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->

@yield('content')

{{--<div class="content">--}}
    {{--<!-- BEGIN LOGIN FORM -->--}}
    {{--<form class="login-form" action="index.html" method="post">--}}
        {{--<h3 class="form-title">Sign In</h3>--}}
        {{--<div class="alert alert-danger display-hide">--}}
            {{--<button class="close" data-close="alert"></button>--}}
			{{--<span>--}}
			{{--Enter any username and password. </span>--}}
        {{--</div>--}}
        {{--<div class="form-group">--}}
            {{--<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->--}}
            {{--<label class="control-label visible-ie8 visible-ie9">Username</label>--}}
            {{--<input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username"/>--}}
        {{--</div>--}}
        {{--<div class="form-group">--}}
            {{--<label class="control-label visible-ie8 visible-ie9">Password</label>--}}
            {{--<input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password"/>--}}
        {{--</div>--}}
        {{--<div class="form-actions">--}}
            {{--<button type="submit" class="btn btn-success uppercase">Login</button>--}}
            {{--<label class="rememberme check">--}}
                {{--<input type="checkbox" name="remember" value="1"/>Remember </label>--}}
            {{--<a href="javascript:;" id="forget-password" class="forget-password">Forgot Password?</a>--}}
        {{--</div>--}}
    {{--</form>--}}
{{--</div>--}}
<div class="copyright">
    2016 © Soft-BD Ltd.
</div>

<script>
    jQuery(document).ready(function() {
        Metronic.init(); // init metronic core components
        Layout.init(); // init current layout
        Login.init();
        Demo.init();
    });
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>