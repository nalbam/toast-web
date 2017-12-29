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
                        <a href="/fleet/item/<?= @$fleet->no ?>"><?= @$fleet->fleet ?></a>
                    <? } ?>
                </h3>
            </div>
            <div class="title_right">
                <button id="fleet_save" title="save" data-toggle="tooltip" data-placement="top" class="btn btn-form btn-default btn-sm"><i class="fa fa-floppy-o primary"></i></button>
            </div>
        </div>

        <div class="clearfix"></div>

        <form id="form_fleet_save" action="/fleet/save/<?= @$fleet->no ?>" onsubmit="return false;">

            <? if (empty(@$fleet->fleet)) { ?>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Info</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>phase</th>
                                        <th>fleet</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <select name="phase" onchange="reload_fleet(this.value)" class="form-control input-sm">
                                                <? foreach ($phases as $item) { ?>
                                                    <option value="<?= $item->key ?>" <?= ($fleet->phase == $item->key) ? 'selected="selected"' : '' ?>><?= $item->key ?></option>
                                                <? } ?>
                                            </select>
                                        </td>
                                        <td><input type="text" name="fleet" value="<?= @$fleet->fleet ?>" class="form-control input-sm" placeholder="fleet"/></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <? } ?>

            <? if (@$fleet->phase !== 'local' && @$fleet->phase !== 's3') { ?>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Instance</h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">

                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>type <a href="/help/instance" target="_blank"><i class='fa fa-question-circle-o'></i></a></th>
                                        <th>image</th>
                                        <th>vpc</th>
                                        <th>subnet</th>
                                        <th>security groups</th>
                                        <? if (@$fleet->phase !== 'lb') { ?>
                                            <th>lb</th>
                                        <? } ?>
                                        <th>ip</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <select id="aws_type" name="type" class="form-control input-sm">
                                                <option value="">- none -</option>
                                                <? foreach ($types as $item) { ?>
                                                    <option value="<?= $item->type ?>" <?= $item->type == @$instance->type ? 'selected="selected"' : '' ?>><?= $item->type ?></option>
                                                <? } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="aws_image" name="image" class="form-control input-sm">
                                                <option value="">- none -</option>
                                                <? if (!empty(@$instance->image)) { ?>
                                                    <option value="<?= @$instance->image ?>" selected="selected"><?= @$instance->image ?></option>
                                                <? } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="aws_vpc" name="vpc" class="form-control input-sm">
                                                <option value="">- none -</option>
                                                <? if (!empty(@$instance->vpc)) { ?>
                                                    <option value="<?= @$instance->vpc ?>" selected="selected"><?= @$instance->vpc ?></option>
                                                <? } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="aws_subnet" name="subnet[]" size="5" multiple class="form-control input-sm">
                                                <option value="">- none -</option>
                                                <? if (count(@$instance->subnet) > 0) { ?>
                                                    <? foreach ($instance->subnet as $item) { ?>
                                                        <option value="<?= $item ?>" selected="selected"><?= $item ?></option>
                                                    <? } ?>
                                                <? } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="aws_security" name="security[]" size="5" multiple class="form-control input-sm">
                                                <option value="">- none -</option>
                                                <? if (count(@$instance->security) > 0) { ?>
                                                    <? foreach ($instance->security as $item) { ?>
                                                        <option value="<?= $item ?>" selected="selected"><?= $item ?></option>
                                                    <? } ?>
                                                <? } ?>
                                            </select>
                                        </td>
                                        <? if (@$fleet->phase != 'lb') { ?>
                                            <td>
                                                <select name="lb_f_no" class="form-control input-sm">
                                                    <option value="0">- none -</option>
                                                    <? foreach ($lbs as $item) { ?>
                                                        <option value="<?= $item->no ?>"
                                                            <?= $item->no == @$fleet->lb_f_no ? 'selected="selected"' : '' ?>><?= $item->fleet ?></option>
                                                    <? } ?>
                                                </select>
                                            </td>
                                        <? } ?>
                                        <td>
                                            <div class="radio">
                                                <label><input type="radio" id="aws_ip2" name="ip" value="public" <?= @$instance->ip != 'elastic' ? 'checked="checked"' : '' ?>> public</label>
                                            </div>
                                            <div class="radio">
                                                <label><input type="radio" id="aws_ip3" name="ip" value="elastic" <?= @$instance->ip == 'elastic' ? 'checked="checked"' : '' ?>> elastic</label>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <? if (@$fleet->phase == 'lb') { ?>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>http port</th>
                                            <th>https port</th>
                                            <th><a href="/certificate">certificate</a></th>
                                            <th>tcp ports</th>
                                            <th>custom location</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <input type="text" name="lb_http" value="<?= @$lb->http ?>" class="form-control input-sm" placeholder="80"/>
                                            </td>
                                            <td>
                                                <input type="text" name="lb_https" value="<?= @$lb->https ?>" class="form-control input-sm" placeholder="443"/>
                                            </td>
                                            <td>
                                                <select name="lb_ssl" class="form-control input-sm">
                                                    <option value="">- none -</option>
                                                    <? foreach ($certificate as $item) { ?>
                                                        <option value="<?= $item->name ?>"
                                                            <?= $item->name == @$lb->ssl ? 'selected="selected"' : '' ?>><?= $item->name ?></option>
                                                    <? } ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="lb_tcp" value="<?= @$lb->tcp ?>" class="form-control input-sm" placeholder="8080 8081"/>
                                            </td>
                                            <td>
                                                <label for="lb_custom_n" class="radio-inline"><input type="radio" id="lb_custom_n" name="lb_custom" value="" class="lb_custom" <?= empty(@$lb->custom) ? 'checked="checked"' : '' ?>> default</label>
                                                <label for="lb_custom_r" class="radio-inline"><input type="radio" id="lb_custom_r" name="lb_custom" value="R" class="lb_custom" <?= (@$lb->custom->type == 'R') ? 'checked="checked"' : '' ?>> replace</label>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <table id="lb_custom" class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>
                                                custom config
                                                <a href="/help/lb" target="_blank"><i class='fa fa-question-circle-o'></i></a>
                                                &nbsp;
                                                <label for="lb_custom_s" class="radio-inline"><input type="radio" id="lb_custom_s" name="lb_custom" value="S" class="lb_custom" <?= (@$lb->custom->type == 'S') ? 'checked="checked"' : '' ?>> https redirect</label>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <textarea name="lb_custom_config" rows="5" class="form-control input-sm"><?= @$lb->custom->config ? @$lb->custom->config : @$lb->custom->http ?></textarea>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                <? } else { ?>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>tcp ports (for lb)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <input type="text" name="lb_tcp" value="<?= @$lb->tcp ?>" class="form-control input-sm" placeholder="8080 8081"/>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                <? } ?>

                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th width="50%">hosts</th>
                                        <th width="50%">profile</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td><textarea id="hosts" name="hosts" rows="3" class="form-control input-sm"><?= @$fleet->hosts ?></textarea></td>
                                        <td><textarea id="profile" name="profile" rows="3" class="form-control input-sm"><?= @$fleet->profile ?></textarea></td>
                                    </tr>
                                    </tbody>
                                </table>

                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>web</th>
                                        <? if (@$fleet->phase != 'lb') { ?>
                                            <th>php</th>
                                            <th>node</th>
                                            <th>java</th>
                                            <th>tomcat</th>
                                        <? } ?>
                                        <th>health</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <? if (@$fleet->phase != 'lb') { ?>
                                                <label for="apps_httpd" class="checkbox-inline">
                                                    <input type="checkbox" id="apps_httpd" name="apps[]" value="httpd" class="one_web" <?= _contains(@$fleet->apps, 'httpd') ? 'checked="checked"' : '' ?>> httpd
                                                </label>
                                            <? } else { ?>
                                                <label for="apps_nginx" class="checkbox-inline">
                                                    <input type="checkbox" id="apps_nginx" name="apps[]" value="nginx" class="one_web" <?= _contains(@$fleet->apps, 'nginx') ? 'checked="checked"' : '' ?>> nginx
                                                </label>
                                            <? } ?>
                                        </td>
                                        <? if (@$fleet->phase != 'lb') { ?>
                                            <td>
                                                <label for="apps_php55" class="checkbox-inline">
                                                    <input type="checkbox" id="apps_php55" name="apps[]" value="php55" class="one_php" <?= _contains(@$fleet->apps, 'php55') ? 'checked="checked"' : '' ?>> php55
                                                </label>
                                                <label for="apps_php56" class="checkbox-inline">
                                                    <input type="checkbox" id="apps_php56" name="apps[]" value="php56" class="one_php" <?= _contains(@$fleet->apps, 'php56') ? 'checked="checked"' : '' ?>> php56
                                                </label>
                                                <label for="apps_php70" class="checkbox-inline">
                                                    <input type="checkbox" id="apps_php70" name="apps[]" value="php70" class="one_php" <?= _contains(@$fleet->apps, 'php70') ? 'checked="checked"' : '' ?>> php70
                                                </label>
                                            </td>
                                            <td>
                                                <label for="apps_node" class="checkbox-inline">
                                                    <input type="checkbox" id="apps_node" name="apps[]" value="node" <?= _contains(@$fleet->apps, 'node') ? 'checked="checked"' : '' ?>> node
                                                </label>
                                            </td>
                                            <td>
                                                <label for="apps_java8" class="checkbox-inline">
                                                    <input type="checkbox" id="apps_java8" name="apps[]" value="java8" <?= _contains(@$fleet->apps, 'java8') ? 'checked="checked"' : '' ?>> java8
                                                </label>
                                            </td>
                                            <td>
                                                <label for="apps_tomcat8" class="checkbox-inline">
                                                    <input type="checkbox" id="apps_tomcat8" name="apps[]" value="tomcat8" <?= _contains(@$fleet->apps, 'tomcat8') ? 'checked="checked"' : '' ?>> tomcat8
                                                </label>
                                            </td>
                                        <? } ?>
                                        <td>
                                            <input type="text" name="health" value="<?= @$fleet->health ?>" class="form-control input-sm" placeholder="80"/>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            <? } ?>

        </form>

        <div class="clearfix"></div>

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

<div id="md-confirm" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">경고</h4>
            </div>
            <div id="md-confirm-msg" class="modal-body">
                <h4>정말 삭제 할까요?</h4>
                <p>삭제 작업이 시작되면 되돌릴 수 없습니다.<br>서버에 저장된 모든 정보가 삭제됩니다.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <span id="md-confirm-btn"><button type="button" class="btn btn-danger">Terminate</button></span>
            </div>
        </div>
    </div>
</div>

<script>
    let arr_web = ['apache', 'nginx'];
    let arr_php = ['php55', 'php56', 'php70'];

    let arr_image = [<?= !empty(@$instance->image) ? '\'' . @$instance->image . '\'' : '' ?>];
    let arr_vpc = [<?= !empty(@$instance->vpc) ? '\'' . @$instance->vpc . '\'' : '' ?>];
    let arr_subnet = [<?= !empty(@$instance->subnet) ? '\'' . join('\',\'', @$instance->subnet) . '\'' : '' ?>];
    let arr_security = [<?= !empty(@$instance->security) ? '\'' . join('\',\'', @$instance->security) . '\'' : ''?>];

    $(function () {
        aws_load('image');
        aws_load('vpc');

        $("#aws_vpc").change(function () {
            aws_load('subnet');
            aws_load('security');
        });

        show_custom('<?= @$lb->custom->type ?>');

        $(".one_web").click(function () {
            let chk = $(this).is(':checked');
            console.log('this.id : ' + this.id + ' checked ' + chk);
            if (chk) {
                for (let i in arr_web) {
                    if (this.id !== 'apps_' + arr_web[i]) {
                        $('#apps_' + arr_web[i]).prop('checked', false);
                    }
                }
            }
        });
        $(".one_php").click(function () {
            let chk = $(this).is(':checked');
            console.log('this.id : ' + this.id + ' checked ' + chk);
            if (chk) {
                for (let i in arr_php) {
                    if (this.id !== 'apps_' + arr_php[i]) {
                        $('#apps_' + arr_php[i]).prop('checked', false);
                    }
                }
            }
        });
        $(".lb_custom").click(function () {
            console.log('this.id : ' + this.id + ' checked ' + this.value);
            show_custom(this.value);
        });
    });

    function show_custom(v) {
        if (v === '') {
            $('#lb_custom').hide();
        } else {
            $('#lb_custom').show();
        }
    }

    function reload_fleet(v) {
        location.replace('/fleet/form/<?= @$fleet->no ?>?phase=' + v);
    }
</script>
<script src="/static/js/aws.js?<?= VERSION ?>"></script>
