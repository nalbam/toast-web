<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= SITE_NAME ?><?= $org->name ? ' :: ' . $org->name : '' ?></title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/<?= BOOTSTRAP ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/<?= BOOTSTRAP ?>/css/bootstrap-theme.min.css">
    <!-- Font Awesome -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="/gentelella/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Datatables -->
    <link href="/gentelella/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="/gentelella/css/custom.min.css?<?= VERSION ?>" rel="stylesheet">
    <link href="/static/css/style.css?<?= VERSION ?>" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-<?= JQUERY ?>.min.js"></script>
</head>
<body class="login">
<div>
    <div class="login_wrapper">
        <div class="animate form login_form">
            <section class="login_content">
                <h1>Login</h1>

                <!--<div class="clearfix"></div>-->

                <!--<form id="form_login" action="--><? //= YWT_LOGIN ?><!--" onsubmit="return false;">-->
                <!--<input type="hidden" name="auto" value="on"/>-->
                <!--<div>-->
                <!--<input type="text" name="id" class="form-control" placeholder="id" required=""/>-->
                <!--</div>-->
                <!--<div>-->
                <!--<input type="password" name="passwd" class="form-control" placeholder="password" required=""/>-->
                <!--</div>-->
                <!--<div>-->
                <!--<button id="login" class="btn btn-form btn-default btn-sm"><i class="fa fa-sign-in primary"></i></button>-->
                <!--<a class="reset_pass" href="--><? //= MEMBER_URL ?><!--/member/find?pageType=pw">Lost your password?</a>-->
                <!--</div>-->
                <!--</form>-->

                <div class="clearfix"></div>

                <div class="separator">
                    <a href="/home/github" class="btn btn-primary btn-lg"><span class="fa fa-github"></span> Sign in with Github</a>
                </div>

                <div class="clearfix"></div>

                <div class="separator">
                    <div>
                        <h1><img src="/static/img/toast-64.png" width="32" height="32"> <?= SITE_NAME ?></h1>
                        <p>&copy; toast.sh 2018</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Bootstrap -->
<script src="//maxcdn.bootstrapcdn.com/bootstrap/<?= BOOTSTRAP ?>/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="/gentelella/vendors/fastclick/lib/fastclick.js"></script>
<!-- NProgress -->
<script src="/gentelella/vendors/nprogress/nprogress.js"></script>
<!-- Datatables -->
<script src="/gentelella/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="/gentelella/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- Custom Theme Scripts -->
<script src="/gentelella/js/custom.min.js"></script>

<script src="/static/js/form.js?<?= VERSION ?>"></script>
<script src="/static/js/alert.js?<?= VERSION ?>"></script>

</body>
</html>
