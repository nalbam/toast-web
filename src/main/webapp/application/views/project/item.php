<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Project : <?= $project->name ?></h3>
            </div>
            <div class="title_right">
                <?= empty($project->git_url) ? '' : '<a href="' . $project->git_url . '" target="_blank"><img src="/static/img/github.png" width="24" height="24"></a>' ?>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Info</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <form id="form_project_save_<?= $project->no ?>" action="/project/save/<?= $project->no ?>" onsubmit="return false;">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>job name</th>
                                    <th>group id</th>
                                    <th>artifact id</th>
                                    <th>next version</th>
                                    <th>type</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><input type="text" name="name" value="<?= $project->name ?>" class="form-control input-sm" placeholder="job name"/></td>
                                    <td><input type="text" name="groupId" value="<?= $project->groupId ?>" class="form-control input-sm" placeholder="group id"/></td>
                                    <td><input type="text" name="artifactId" value="<?= $project->artifactId ?>" class="form-control input-sm" placeholder="artifact id"/></td>
                                    <td>
                                        <input type="text" id="major" name="major" size="2" value="<?= $project->major ?>" readonly="readonly"/>
                                        <button id="major_plus" class="btn btn-form btn-default btn-xs version"><i class="fa fa-plus primary"></i></button>
                                        .
                                        <input type="text" id="minor" name="minor" size="2" value="<?= $project->minor ?>" readonly="readonly"/>
                                        <button id="minor_plus" class="btn btn-form btn-default btn-xs version"><i class="fa fa-plus primary"></i></button>
                                        .
                                        <input type="text" id="build" name="build" size="2" value="<?= $project->build ?>" readonly="readonly"/>
                                        <button id="build_plus" class="btn btn-form btn-default btn-xs version"><i class="fa fa-plus primary"></i></button>
                                    </td>
                                    <td>
                                        <select name="packaging" class="form-control input-sm">
                                            <option value="web" <?= $project->packaging == 'web' ? 'selected="selected"' : '' ?>>web</option>
                                            <option value="jar" <?= $project->packaging == 'jar' ? 'selected="selected"' : '' ?>>jar</option>
                                            <option value="war" <?= $project->packaging == 'war' ? 'selected="selected"' : '' ?>>war</option>
                                            <option value="zip" <?= $project->packaging == 'zip' ? 'selected="selected"' : '' ?>>zip</option>
                                            <option value="php" <?= $project->packaging == 'php' ? 'selected="selected"' : '' ?>>php</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button id="project_save_<?= $project->no ?>" class="btn btn-form btn-default btn-sm"><i class="fa fa-floppy-o primary"></i></button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <? if ($project->no > 0) { ?>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Branch</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>branch</th>
                                    <th>build</th>
                                    <th>status</th>
                                    <th>health</th>
                                    <th>last build</th>
                                    <th>last success</th>
                                    <th>last failed</th>
                                </tr>
                                </thead>
                                <tbody id="jobs">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Targets</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>phase : fleet</th>
                                    <th>version</th>
                                    <th>domain</th>
                                    <th>port</th>
                                    <th>deploy</th>
                                    <th>le</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <? foreach ($targets as $target) { ?>
                                    <form id="form_target_save_<?= $target->no ?>" action="/target/save/<?= $target->no ?>" onsubmit="return false;">
                                        <tr id="target-<?= $target->no ?>">
                                            <td><a href="/fleet/item/<?= $target->f_no ?>"><?= $target->phase ?> : <?= $target->fleet ?></a></td>
                                            <td>
                                                <select name="version" class="form-control input-sm">
                                                    <option value="0.0.0">0.0.0</option>
                                                    <? foreach ($versions as $version) { ?>
                                                        <option value="<?= $version->version ?>" <?= $version->version == $target->version ? 'selected="selected"' : '' ?>><?= $version->version ?></option>
                                                    <? } ?>
                                                </select>
                                            </td>
                                            <td><input type="text" name="domain" value="<?= $target->domain ?>" class="form-control input-sm"/></td>
                                            <td>
                                                <input type="text" name="port" value="<?= $target->port ?>" placeholder="80" size="5" class="form-control input-sm"/>
                                            </td>
                                            <td>
                                                <select name="deploy" class="form-control input-sm">
                                                    <option value="web" <?= $target->deploy == 'web' ? 'selected="selected"' : '' ?>>web</option>
                                                    <option value="jar" <?= $target->deploy == 'jar' ? 'selected="selected"' : '' ?>>jar</option>
                                                    <option value="war" <?= $target->deploy == 'war' ? 'selected="selected"' : '' ?>>war</option>
                                                    <option value="s3" <?= $target->deploy == 's3' ? 'selected="selected"' : '' ?>>s3</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="le" value="Y" <?= $target->le == 'Y' ? 'checked="checked"' : '' ?>/>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button id="target_save_<?= $target->no ?>" class="btn btn-form btn-default btn-sm"><i class="fa fa-floppy-o primary"></i></button>
                                                    <button id="target_remove_<?= $target->no ?>" class="btn btn-call btn-default btn-sm"><i class="fa fa-trash primary"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </form>
                                <? } ?>
                                <form id="form_target_save" action="/target/save" onsubmit="return false;">
                                    <tr>
                                        <td>
                                            <select name="fleet" class="form-control input-sm">
                                                <option value="">- phase : fleet -</option>
                                                <? foreach ($fleets as $item) { ?>
                                                    <option value="<?= $item->no ?>"><?= $item->phase ?> : <?= $item->fleet ?></option>
                                                <? } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="version" class="form-control input-sm">
                                                <option value="0.0.0">0.0.0</option>
                                                <? foreach ($versions as $item) { ?>
                                                    <option value="<?= $item->version ?>"><?= $item->version ?></option>
                                                <? } ?>
                                            </select>
                                        </td>
                                        <td><input type="text" name="domain" value="" class="form-control input-sm"/></td>
                                        <td>
                                            <input type="text" name="port" value="" placeholder="80" size="5" class="form-control input-sm"/>
                                        </td>
                                        <td>
                                            <select name="deploy" class="form-control input-sm">
                                                <option value="web" <?= $project->packaging == 'web' ? 'selected="selected"' : '' ?>>web</option>
                                                <option value="jar" <?= $project->packaging == 'jar' ? 'selected="selected"' : '' ?>>jar</option>
                                                <option value="war" <?= $project->packaging == 'war' ? 'selected="selected"' : '' ?>>war</option>
                                                <option value="s3" <?= $project->packaging == 's3' ? 'selected="selected"' : '' ?>>s3</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="checkbox" name="le" value="Y"/>
                                        </td>
                                        <td>
                                            <button id="target_save" class="btn btn-form btn-default btn-sm"><i class="fa fa-floppy-o primary"></i></button>
                                            <input type="hidden" name="p_no" value="<?= $project->no ?>"/>
                                        </td>
                                    </tr>
                                </form>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Versions</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>version</th>
                                    <th>status</th>
                                    <th>created</th>
                                    <th>updated</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <? foreach ($versions as $version) { ?>
                                    <tr id="version-<?= $version->no ?>">
                                        <td><a href="/version/item/<?= $version->no ?>"><?= $version->version ?></a></td>
                                        <td>
                                            <form id="form_version_status_<?= $version->no ?>" action="/version/status/<?= $version->no ?>" onsubmit="return false;">
                                                <label>
                                                    <select name="status" class="form-control input-sm">
                                                        <? foreach ($statuses as $status) { ?>
                                                            <option value="<?= $status->code ?>" <?= $status->code == $version->status ? 'selected="selected"' : '' ?>><?= $status->name ?></option>
                                                        <? } ?>
                                                    </select>
                                                </label>
                                                <? if (empty($version->status) || $version->status == '10') echo "&nbsp; <i class='fa fa-exclamation-circle warning'></i>"; ?>
                                            </form>
                                            <form id="form_version_remove_<?= $version->no ?>" action="/version/remove/<?= $version->no ?>" onsubmit="return false;"></form>
                                        </td>
                                        <td>
                                            <?= $version->reg_date ?>
                                            <? if ($version->reg_days < 2) echo "&nbsp; <i class='fa fa-paw warning'></i>"; ?>
                                        </td>
                                        <td>
                                            <?= $version->mod_date ?>
                                            <? if ($version->mod_days < 2) echo "&nbsp; <i class='fa fa-paw warning'></i>"; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button id="version_status_<?= $version->no ?>" class="btn btn-form btn-default btn-sm"><i class="fa fa-floppy-o primary"></i></button>
                                                <button id="version_remove_<?= $version->no ?>" onclick="version_remove(this.id)" class="btn btn-default btn-sm"><i class="fa fa-trash primary"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <? } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>

    </div>
</div>
<!-- /page content -->

<div id="md-version-remove" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">경고</h4>
            </div>
            <div id="md-version-remove-msg" class="modal-body">
                <h4>정말 Version 을 삭제 할까요?</h4>
                <p>삭제 작업이 시작되면 되돌릴 수 없습니다.<br>버전 패키지의 모든 정보가 삭제됩니다.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <span id="md-version-remove-btn"><button type="button" class="btn btn-danger">Remove</button></span>
            </div>
        </div>
    </div>
</div>

<? if ($project->no > 0) { ?>
    <script>
        let no = "<?= $project->no ?>";
        let name = "<?= $project->name ?>";
        let last = "<?= @$last ?>";
    </script>
    <script src="/static/js/jenkins.js?<?= VERSION ?>"></script>
    <script src="/static/js/version.js?<?= VERSION ?>"></script>
<? } ?>
