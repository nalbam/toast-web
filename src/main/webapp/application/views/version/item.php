<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Version : <?= $project->name ?> <?= $version->version ?></h3>
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
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>job name</th>
                                <th>group id</th>
                                <th>artifact id</th>
                                <th>version</th>
                                <th>type</th>
                                <th>status</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><?= $project->name ?></td>
                                <td><?= $project->groupId ?></td>
                                <td><?= $project->artifactId ?></td>
                                <td><?= $version->version ?></td>
                                <td><?= $project->packaging ?></td>
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
                                </td>
                                <td>
                                    <button id="version_status_<?= $version->no ?>" class="btn btn-form btn-default btn-sm"><i class="fa fa-floppy-o primary"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>release note</td>
                                <td colspan="5">
                                    <form id="form_version_save_<?= $version->no ?>" action="/version/save/<?= $version->no ?>" onsubmit="return false;">
                                        <textarea id="note" name="note" rows="15" class="form-control input-sm"><?= $version->note ?></textarea>
                                    </form>
                                </td>
                                <td>
                                    <button id="version_save_<?= $version->no ?>" class="btn btn-form btn-default btn-sm"><i class="fa fa-floppy-o primary"></i></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- /page content -->
