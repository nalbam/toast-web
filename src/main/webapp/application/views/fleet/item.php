<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Fleet :
                    <? if (empty(@$fleet->fleet)) { ?>
                        unnamed
                    <? } else { ?>
                        <a href="/phase/item/<?= @$fleet->phase ?>"><?= @$fleet->phase ?></a> &gt;
                        <?= @$fleet->fleet ?>
                    <? } ?>
                </h3>
            </div>
            <div class="title_right">
                <? if (!empty(@$fleet->fleet)) { ?>
                    <? if (@$fleet->phase !== 's3') { ?>
                        <a href="/fleet/form/<?= $fleet->no ?>" title="edit" data-toggle="tooltip" data-placement="top" class="btn btn-form btn-default btn-sm"><i class="fa fa-pencil-square-o primary"></i></a>
                        <button id="fleet_security_<?= $fleet->no ?>" title="security group" data-toggle="tooltip" data-placement="top" class="btn btn-call btn-default btn-sm"><i class="fa fa-shield primary"></i></button>
                        <button id="aws_launch_<?= $fleet->no ?>" title="launch" data-toggle="tooltip" data-placement="top" class="btn btn-call btn-default btn-sm"><i class="fa <?= ICON_LAUNCH ?> primary"></i></button>
                    <? } ?>
                <? } ?>
            </div>
        </div>

        <div class="clearfix"></div>

        <? if (!empty(@$fleet->fleet) && @$fleet->phase != 's3') { ?>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Server List</h2>
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
        <? } ?>

        <? if (!empty(@$fleet->fleet) && @$fleet->phase != 'lb') { ?>
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
                    <div class="x_title">
                        <h2>IP Pool</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>allocation id</th>
                                <th>elastic ip</th>
                                <th>instance id</th>
                                <th>server name</th>
                                <th>
                                    <button id="ip_create_<?= @$fleet->no ?>" onclick="btn_call(this.id)" class="btn btn-default btn-sm"><i class="fa fa-plus primary"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="tb_ip"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

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

<div id="md-ip-remove" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">경고</h4>
            </div>
            <div id="md-ip-remove-msg" class="modal-body">
                <h4>정말 Elastic-IP 를 삭제 할까요?</h4>
                <p>삭제 작업이 시작되면 되돌릴 수 없습니다.<br>IP 와 연결된 정보에 문제가 발생 할수 있습니다.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <span id="md-ip-remove-btn"><button type="button" class="btn btn-danger">Remove</button></span>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        // server
        load_server('fleet', '<?= @$fleet->no ?>');
        setInterval(function () {
            load_server('fleet', '<?= @$fleet->no ?>');
        }, 10000);
    });
</script>
<script src="/static/js/load.js?<?= VERSION ?>"></script>
