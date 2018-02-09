<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= SITE_NAME ?><?= $org->name ? ' :: ' . $org->name : '' ?></title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="/static/bootstrap/<?= BOOTSTRAP ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/bootstrap/<?= BOOTSTRAP ?>/css/bootstrap-theme.min.css">
    <!-- Font Awesome -->
    <link href="/static/font-awesome/<?= FONT_AWESOME ?>/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="/gentelella/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Datatables -->
    <link href="/gentelella/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="/gentelella/css/custom.min.css?<?= VERSION ?>" rel="stylesheet">
    <!-- Custom Style -->
    <link href="/static/css/style.css?<?= VERSION ?>" rel="stylesheet">
    <!-- jQuery -->
    <script src="/static/jquery/jquery-<?= JQUERY ?>.min.js"></script>
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                    <a href="/" class="site_title">
                        <img src="/static/img/toast-64.png" width="32" height="32">
                        <span><?= SITE_NAME ?><?= $org->name ? ' :: ' . $org->name : '' ?></span>
                    </a>
                </div>

                <div class="clearfix"></div>

                <!-- menu profile quick info -->
                <? if (!empty($user)) { ?>
                    <div class="profile">
                        <div class="profile_pic">
                            <img src="<?= $user->picture ?>" alt="..." class="img-circle profile_img">
                        </div>
                        <div class="profile_info">
                            <span>Welcome,</span>
                            <h2><?= $user->nickname ?></h2>
                        </div>
                    </div>
                <? } ?>
                <!-- /menu profile quick info -->

                <div class="clearfix"></div>

                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <h3>&nbsp;</h3>
                        <ul class="nav side-menu">
                            <li><a href="/"><i class="fa fa-home"></i> Home</a></li>
                            <li><a href="/phase"><i class="fa fa-sliders"></i>Phase</a></li>
                            <!-- <li><a href="/earth"><i class="fa fa-globe"></i>Earth</a></li> -->
                            <li><a href="/fleet"><i class="fa fa-ship"></i>Fleet</a></li>
                            <li><a href="/server"><i class="fa fa-server"></i> Server</a></li>
                            <li><a href="/project"><i class="fa fa-cube"></i> Project</a></li>
                            <? if (strpos($auth, 'system') !== false) { ?>
                                <li><a href="/config"><i class="fa fa-cog"></i> Config</a></li>
                                <li><a href="/certificate"><i class="fa fa-certificate"></i> Certificate</a></li>
                            <? } ?>
                            <li><a href="/ip"><i class="fa fa-chess"></i> Ip</a></li>
                            <li><a href="/user"><i class="fa fa-users"></i> User</a></li>
                            <li><a href="/dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                        </ul>
                    </div>
                </div>
                <!-- /sidebar menu -->
            </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
            <div class="nav_menu">
                <nav>
                    <div class="nav toggle">
                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                    </div>
                    <? if (empty($user)) { ?>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="/home/login?redirect_url=<?= THIS_URL ?>">Login</a></li>
                        </ul>
                    <? } else { ?>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="">
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <img src="<?= $user->picture ?>" alt=""><?= $user->nickname ?> <span class=" fa fa-angle-down"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-usermenu pull-right">
                                    <li><a href="/home/auth"> Auth</a></li>
                                    <li><a href="/home/logout"><i class="fa fa-sign-out pull-right"></i> Logout</a></li>
                                </ul>
                            </li>
                        </ul>
                    <? } ?>
                </nav>
            </div>
        </div>
        <!-- /top navigation -->
