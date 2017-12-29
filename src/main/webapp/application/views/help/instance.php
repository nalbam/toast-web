<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Help</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Instance Type (<?= count($list) ?>)</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>type</th>
                                <th>cpu</th>
                                <th>mem</th>
                                <th>price (1h)</th>
                                <th>price (30d)</th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($list as $item) { ?>
                                <tr>
                                    <td><?= $item->type ?></td>
                                    <td class="text-right"><?= $item->cpu ?></td>
                                    <td class="text-right"><?= number_format($item->mem, 2) ?></td>
                                    <td class="text-right">$ <?= number_format($item->price, 3) ?></td>
                                    <td class="text-right">$ <?= number_format($item->price * 24 * 30, 2) ?></td>
                                </tr>
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
