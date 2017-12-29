<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Config</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Config List (<?= count($list) ?>)</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>key</th>
                                <th>desc</th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($list as $item) { ?>
                                <? if ($item->key == '---') { ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                <? } else { ?>
                                    <tr>
                                        <td><a href="/config/item/<?= $item->key ?>"><?= $item->key ?></a></td>
                                        <td><?= $item->desc ?></td>
                                    </tr>
                                <? } ?>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- /page content -->
