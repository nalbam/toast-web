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
                                <th></th>
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
                                <th>public ip</th>
                                <th>host</th>
                                <th>port</th>
                                <th>user</th>
                                <th>health</th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($list as $item) { ?>
                                <tr id="server-<?= $item->no ?>">
                                    <td>
                                        <? if (empty($item->u_no)) { ?>
                                            <button id="server_star_<?= $item->no ?>" class="btn btn-call btn-default btn-sm"><i class="fa fa-star-o star primary"></i></button>
                                        <? } else { ?>
                                            <button id="server_star_<?= $item->no ?>" class="btn btn-call btn-default btn-sm"><i class="fa fa-star star primary"></i></button>
                                        <? } ?>
                                    </td>
                                    <td><a href="/server/?phase=<?= $item->phase ?>"><?= $item->phase ?></a></td>
                                    <td><a href="/fleet/item/<?= $item->f_no ?>"><?= $item->fleet ?></a></td>
                                    <td><a href="/server/item/<?= $item->no ?>"><?= $item->name ?></a></td>
                                    <td><a href="/server/item/<?= $item->no ?>" title="<?= @$item->instance->type ?>"><i class="fa fa-server"></i> <?= $item->id ?></a></td>
                                    <td><?= $item->ip ?></td>
                                    <td><?= $item->host ?></td>
                                    <td><?= $item->port ?></td>
                                    <td><?= $item->user ?></td>
                                    <td>
                                        <? if (!empty($item->host)) { ?>
                                            <?= $item->h->ping->s < 66 ? "<i class='fa fa-check success'></i>" : "<i class='fa fa-exclamation danger'></i> (" . $item->h->ping->d . ")" ?>
                                        <? } else { ?>
                                            <i class='fa fa-exclamation danger'></i>
                                        <? } ?>
                                        <? if (!empty($item->host)) { ?>
                                            <?= $item->h->pong->s < 66 ? "<i class='fa fa-check success'></i>" : "<i class='fa fa-exclamation danger'></i> (" . $item->h->pong->d . ")" ?>
                                        <? } else { ?>
                                            <i class='fa fa-exclamation danger'></i>
                                        <? } ?>
                                    </td>
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
