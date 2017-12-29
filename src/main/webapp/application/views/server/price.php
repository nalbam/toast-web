<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Server</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Server List (<?= count($list) ?>)</h2>
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
                                <th>name</th>
                                <th>instance id</th>
                                <th>instance type <a href="/help/instance"><i class='fa fa-question-circle-o'></i></a></th>
                                <th>prev month</th>
                                <th>this month</th>
                                <th>per hour</th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($list as $item) { ?>
                                <tr id="server-<?= $item->no ?>">
                                    <td><a href="/server/price?phase=<?= $item->phase ?>"><?= $item->phase ?></a></td>
                                    <td><a href="/fleet/item/<?= $item->f_no ?>"><?= $item->fleet ?></a></td>
                                    <td><a href="/server/item/<?= $item->no ?>"><?= $item->name ?></a></td>
                                    <td><a href="/server/item/<?= $item->no ?>"><?= $item->id ?></a></td>
                                    <td><?= $item->instance->type ?></td>
                                    <td id="price-<?= $item->no ?>-<?= $price[0] ?>" class="text-right btn-price"></td>
                                    <td id="price-<?= $item->no ?>-<?= $price[1] ?>" class="text-right btn-price"></td>
                                    <td id="price-<?= $item->no ?>-hour" class="text-right btn-price"></td>
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

<script src="/static/js/price.js?<?= VERSION ?>"></script>
