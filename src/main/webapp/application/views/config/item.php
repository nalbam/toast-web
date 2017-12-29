<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Config : <?= $config->key ?></h3>
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
                        <form id="form_config_save" action="/config/save" onsubmit="return false;">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>key</th>
                                    <th>value</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <?= $config->key ?>
                                        <input type="hidden" name="key" value="<?= $config->key ?>">
                                    </td>
                                    <td>
                                        <? if ($config->type == 'text') { ?>
                                            <input type="text" name="val" class="form-control input-sm" value="<?= $config->val ?>">
                                        <? } else if ($config->type == 'list') { ?>
                                            <select id="aws_<?= @$config->aws ?>" name="val" size="15" class="form-control input-sm">
                                                <option value="">- loading -</option>
                                            </select>
                                        <? } else if ($config->type == 'multi') { ?>
                                            <select id="aws_<?= @$config->aws ?>" name="val[]" size="15" multiple class="form-control input-sm">
                                                <option value="">- loading -</option>
                                            </select>
                                        <? } else { ?>
                                            <textarea name="val" rows="15" class="form-control input-sm"><?= $config->val ?></textarea>
                                        <? } ?>
                                    </td>
                                    <td>
                                        <button id="config_save" class="btn btn-form btn-default btn-sm"><i class="fa fa-floppy-o primary"></i></button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- /page content -->
