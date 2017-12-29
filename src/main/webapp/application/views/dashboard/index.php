<!-- bootstrap-progressbar -->
<link href="/gentelella/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">

<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Dashboard</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">

            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel tile fixed_height_390">
                    <div class="x_title">
                        <h2>Phase</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <? foreach ($list as $item) { ?>
                            <div class="widget_summary">
                                <div class="w_left w_25">
                                    <span><?= $item->key ?></span>
                                </div>
                                <div class="w_center w_55">
                                    <div class="progress">
                                        <div id="pb_bar_<?= $item->key ?>" class="bar progress-bar bg-green" role="progressbar" data-transitiongoal="0"></div>
                                    </div>
                                </div>
                                <div class="w_right w_20">
                                    <span id="pb_cnt_<?= $item->key ?>">0</span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        <? } ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel tile fixed_height_390">
                    <div class="x_title">
                        <h2>Ping</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="dashboard-widget-content">
                            <div class="widget_summary">
                                <canvas width="300" height="180" id="ping" class="" style="width: 100%; height: 100%;"></canvas>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="widget_summary">
                            <div class="mid_center">
                                <h3><span id="ping_text">0 / 0</span></h3>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel tile fixed_height_390">
                    <div class="x_title">
                        <h2>Pong</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="dashboard-widget-content">
                            <div class="widget_summary">
                                <canvas width="300" height="180" id="pong" class="" style="width: 100%; height: 100%;"></canvas>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="widget_summary">
                            <div class="mid_center">
                                <h3><span id="pong_text">0 / 0</span></h3>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="server_list" class="row"></div>

    </div>
</div>
<!-- /page content -->

<!-- gauge.js -->
<script src="/gentelella/vendors/bernii/gauge.js/dist/gauge.min.js"></script>
<!-- /gauge.js -->
<script src="/static/js/dashboard.js?<?= VERSION ?>"></script>
