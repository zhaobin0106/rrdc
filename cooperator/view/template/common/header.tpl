<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-COMPATIBLE" content="IE=edge">
        <title><?php echo $title; ?></title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="<?php echo $static . "AdminLTE-2.3.7/"?>bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo $static . "AdminLTE-2.3.7/"?>dist/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo $static . "AdminLTE-2.3.7/"?>dist/css/ionicons.min.css">
        <link rel="stylesheet" href="<?php echo $static . "AdminLTE-2.3.7/"?>dist/css/AdminLTE.min.css">
        <link rel="stylesheet" href="<?php echo $static . "AdminLTE-2.3.7/"?>dist/css/skins/_all-skins.min.css">
        <link rel="stylesheet" href="<?php echo $static . "AdminLTE-2.3.7/"?>plugins/iCheck/flat/blue.css">
        <link rel="stylesheet" href="<?php echo $static . "AdminLTE-2.3.7/"?>plugins/morris/morris.css">
        <link rel="stylesheet" href="<?php echo $static . "AdminLTE-2.3.7/"?>plugins/jvectormap/jquery-jvectormap-1.2.2.css">
        <link rel="stylesheet" href="<?php echo $static . "AdminLTE-2.3.7/"?>plugins/datepicker/datepicker3.css">
        <link rel="stylesheet" href="<?php echo $static . "AdminLTE-2.3.7/"?>plugins/daterangepicker/daterangepicker.css">
        <link rel="stylesheet" href="<?php echo $static . "AdminLTE-2.3.7/"?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
        <link rel="stylesheet" href="<?php echo $static . "AdminLTE-2.3.7/";?>plugins/bootstrap-switch/bootstrap-switch.min.css" />
        <link rel="stylesheet" href="<?php echo $static ?>js/bootstrap-datetimepicker.min.css">
        <link rel="stylesheet" href="<?php echo $static ?>css/toastr.css">
        <link rel="stylesheet" href="<?php echo $static ?>base.css">
        <link rel="stylesheet" href="<?php echo $static ?>loading.css">

        <script src="<?php echo $static . "AdminLTE-2.3.7/"?>plugins/jQuery/jquery-2.2.3.min.js"></script>
        <script src="<?php echo $static . "AdminLTE-2.3.7/"?>plugins/jQueryUI/jquery-ui.min.js"></script>

        <script>
            $.widget.bridge('uibutton', $.ui.button);
        </script>
        <script src="<?php echo $static . "AdminLTE-2.3.7/";?>bootstrap/js/bootstrap.min.js"></script>
        <script src="<?php echo $static . "AdminLTE-2.3.7/";?>plugins/raphael/raphael-min.js"></script>
        <script src="<?php echo $static . "AdminLTE-2.3.7/";?>plugins/morris/morris.min.js"></script>

        <script src="<?php echo $static . "AdminLTE-2.3.7/";?>plugins/sparkline/jquery.sparkline.min.js"></script>
        <script src="<?php echo $static . "AdminLTE-2.3.7/";?>plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
        <script src="<?php echo $static . "AdminLTE-2.3.7/";?>plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

        <script src="<?php echo $static . "AdminLTE-2.3.7/";?>plugins/knob/jquery.knob.js"></script>
        <script src="<?php echo $static . "AdminLTE-2.3.7/";?>plugins/moment.js"></script>
        <script src="<?php echo $static . "AdminLTE-2.3.7/";?>plugins/daterangepicker/daterangepicker.js"></script>
        <script src="<?php echo $static . "AdminLTE-2.3.7/";?>plugins/datepicker/bootstrap-datepicker.js"></script>
        <script src="<?php echo $static . "AdminLTE-2.3.7/";?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
        <script src="<?php echo $static . "AdminLTE-2.3.7/";?>plugins/slimScroll/jquery.slimscroll.min.js"></script>
        <script src="<?php echo $static . "AdminLTE-2.3.7/";?>plugins/fastclick/fastclick.js"></script>
        <script src="<?php echo $static . "AdminLTE-2.3.7/";?>dist/js/app.min.js"></script>
        <script src="<?php echo $static . "AdminLTE-2.3.7/";?>plugins/bootstrap-switch/bootstrap-switch.min.js" type="text/javascript"></script>

        <script src="<?php echo $static;?>js/bootstrap-datetimepicker.min.js"></script>
        <script src="<?php echo $static;?>js/toastr.js"></script>
        <script src="<?php echo $static;?>js/common.js"></script>
    </head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <header class="main-header">
        <!-- Logo -->
        <a href="index2.html" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b><?php echo $app_name;?></b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b><?php echo $app_name;?></b></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- Messages: style can be found in dropdown.less-->
                    <li class="dropdown messages-menu" style="display: none;">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-envelope-o"></i>
                            <span class="label label-success">4</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have 4 messages</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <li><!-- start message -->
                                        <a href="#">
                                            <div class="pull-left">
                                                <img src="<?php echo $static;?>/AdminLTE-2.3.7/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                                            </div>
                                            <h4>
                                                Support Team
                                                <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                            </h4>
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li>
                                    <!-- end message -->
                                    <li>
                                        <a href="#">
                                            <div class="pull-left">
                                                <img src="<?php echo $static?>/AdminLTE-2.3.7/dist/img/user3-128x128.jpg" class="img-circle" alt="User Image">
                                            </div>
                                            <h4>
                                                AdminLTE Design Team
                                                <small><i class="fa fa-clock-o"></i> 2 hours</small>
                                            </h4>
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="pull-left">
                                                <img src="<?php echo $static;?>/AdminLTE-2.3.7/dist/img/user4-128x128.jpg" class="img-circle" alt="User Image">
                                            </div>
                                            <h4>
                                                Developers
                                                <small><i class="fa fa-clock-o"></i> Today</small>
                                            </h4>
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="pull-left">
                                                <img src="<?php echo $static;?>/AdminLTE-2.3.7/dist/img/user3-128x128.jpg" class="img-circle" alt="User Image">
                                            </div>
                                            <h4>
                                                Sales Department
                                                <small><i class="fa fa-clock-o"></i> Yesterday</small>
                                            </h4>
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="pull-left">
                                                <img src="<?php echo $static;?>/AdminLTE-2.3.7/dist/img/user4-128x128.jpg" class="img-circle" alt="User Image">
                                            </div>
                                            <h4>
                                                Reviewers
                                                <small><i class="fa fa-clock-o"></i> 2 days</small>
                                            </h4>
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="footer"><a href="#">See All Messages</a></li>
                        </ul>
                    </li>
                    <!-- Notifications: style can be found in dropdown.less -->
                    <li class="dropdown notifications-menu" style="display: none">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-warning">10</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have 10 notifications</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the
                                            page and may cause design problems
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-users text-red"></i> 5 new members joined
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-shopping-cart text-green"></i> 25 sales made
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-user text-red"></i> You changed your username
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="footer"><a href="#">View all</a></li>
                        </ul>
                    </li>
                    <!-- Tasks: style can be found in dropdown.less -->
                    <li class="dropdown tasks-menu" style="display: none">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-flag-o"></i>
                            <span class="label label-danger">9</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have 9 tasks</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <h3>
                                                Design some buttons
                                                <small class="pull-right">20%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                    <span class="sr-only">20% Complete</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <!-- end task item -->
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <h3>
                                                Create a nice theme
                                                <small class="pull-right">40%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                    <span class="sr-only">40% Complete</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <!-- end task item -->
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <h3>
                                                Some task I need to do
                                                <small class="pull-right">60%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                    <span class="sr-only">60% Complete</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <!-- end task item -->
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <h3>
                                                Make beautiful transitions
                                                <small class="pull-right">80%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                    <span class="sr-only">80% Complete</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <!-- end task item -->
                                </ul>
                            </li>
                            <li class="footer">
                                <a href="#">View all tasks</a>
                            </li>
                        </ul>
                    </li>
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?php echo $static;?>/AdminLTE-2.3.7/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                            <span class="hidden-xs">Alexander Pierce</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="<?php echo $static; ?>/AdminLTE-2.3.7/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">

                            </li>
                            <!-- Menu Body -->
                            <li class="user-body">
                                <button type="button" class="btn btn-block btn-warning" onclick="location.href='<?php echo $logout_url; ?>'">退出</button>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="<?php echo $static;?>/AdminLTE-2.3.7/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p>Admin</p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
            <!-- search form -->
            <form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search...">
                    <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
                </div>
            </form>
            <!-- /.search form -->
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <?php echo $menu; ?>
        </section>
        <!-- /.sidebar -->
    </aside>
    <div class="ajax-content">
    <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper" id="content-wrapper">