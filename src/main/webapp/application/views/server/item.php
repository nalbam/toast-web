<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Server :
                    <a href="/phase/item/<?= $server->phase ?>"><?= $server->phase ?></a> &gt;
                    <a href="/fleet/item/<?= $server->f_no ?>"><?= $server->fleet ?></a> &gt;
                    <?= $server->name ?>
                </h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Server Info</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>name</th>
                                <th>instance id</th>
                                <th>public ip</th>
                                <th>host</th>
                                <th>port</th>
                                <th>user</th>
                                <th>health</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="tb_server"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>CPU Usage</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <canvas id="chart_cpu"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>HDD Usage</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <canvas id="chart_hdd"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Load Average</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <canvas id="chart_mon"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <? if (!empty(@$instance)) { ?>
            <div class="row">
                <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Instance</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <h1><?= $instance->type ?></h1>
                            <span class="count_bottom"><i class="green"><i class="fa fa-microchip"></i> <?= $instance->cpu ?> CPU / <?= $instance->mem ?> GB</i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-6">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Status</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <h1><?= $status ?></h1>
                            <span class="count_bottom"><i class="green"><i class="fa fa-play-circle-o"></i> <?= $server->reg_date ?></i></span>
                        </div>
                    </div>
                </div>
                <? foreach ($hours as $item) { ?>
                    <div class="col-md-3 col-sm-3 col-xs-6">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2><?= $item->ym ?></h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <h1>$ <?= number_format($item->hs * $instance->price, 2) ?></h1>
                                <span class="count_bottom"><i class="green"><i class="fa fa-clock-o"></i> <?= $item->hs ?> Hours</i></span>
                            </div>
                        </div>
                    </div>
                <? } ?>
            </div>
        <? } ?>

        <? if (!empty(@$server->phase) && @$server->phase != 'lb') { ?>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Target List</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>group id</th>
                                    <th>artifact id</th>
                                    <th>deployed</th>
                                    <th>version</th>
                                    <th>packaging</th>
                                    <th>domain</th>
                                    <th>le</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody id="tb_target"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <p id="output"></p>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- /page content -->

<div id="md-server-remove" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">경고</h4>
            </div>
            <div id="md-server-remove-msg" class="modal-body">
                <h4>정말 Instance 를 삭제 할까요?</h4>
                <p>삭제 작업이 시작되면 되돌릴 수 없습니다.<br>서버에 저장된 모든 정보가 삭제됩니다.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <span id="md-server-remove-btn"><button type="button" class="btn btn-danger">Terminate</button></span>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        // server
        load_server('one', '<?= $server->no ?>');
        setInterval(function () {
            load_server('one', '<?= $server->no ?>');
        }, 10000);

        // chart
        load_chart('<?= $server->no ?>', '<?= @$h ?>');
        setInterval(function () {
            load_chart('<?= $server->no ?>', '<?= @$h ?>');
        }, 60000);

        // target
        if ('<?= $server->phase ?>' !== 'lb') {
            load_target('server', '<?= $server->no ?>');
        }
    });
</script>
<script src="/gentelella/vendors/Chart.js/dist/Chart.min.js"></script>
<script src="/static/js/aws.js?<?= VERSION ?>"></script>
<script src="/static/js/load.js?<?= VERSION ?>"></script>
<script src="/static/js/mon.js?<?= VERSION ?>"></script>
