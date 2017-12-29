<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>IP Pool</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>IP List (<?= count($list) ?>)</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>
                                    <select name="phase" onchange="search_phase(this.value)" class="form-control input-sm">
                                        <option value="">- phase -</option>
                                        <? foreach ($phases as $item) { ?>
                                            <option value="<?= $item->key ?>" <?= ($phase == $item->key) ? 'selected="selected"' : '' ?>><?= $item->key ?></option>
                                        <? } ?>
                                    </select>
                                </th>
                                <th>fleet</th>
                                <th>allocation id</th>
                                <th>elastic ip</th>
                                <th>server id</th>
                                <th>server name</th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($list as $item) { ?>
                                <tr id="ip-<?= $item->no ?>">
                                    <td><a href="/ip/?phase=<?= $item->phase ?>"><?= $item->phase ?></a></td>
                                    <td><a href="/fleet/item/<?= $item->f_no ?>"><?= $item->fleet ?></a></td>
                                    <td><?= $item->id ?></td>
                                    <td><?= $item->ip ?></td>
                                    <td><?= $item->s_id ?></td>
                                    <td><?= $item->name ?></td>
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
