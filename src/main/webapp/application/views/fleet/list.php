<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Fleet</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Fleet List (<?= count($list) ?>)</h2>
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
                                <th>instance</th>
                                <th>apps</th>
                                <th>servers</th>
                                <th>
                                    <a href="/fleet/form?phase=<?= $phase ?>" title="new fleet" data-toggle="tooltip" data-placement="top" class="btn btn-form btn-default btn-sm"><i class="fa fa-plus primary"></i></a>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($list as $item) { ?>
                                <tr id="fleet-<?= $item->no ?>">
                                    <td>
                                        <? if (empty($item->u_no)) { ?>
                                            <button id="fleet_star_<?= $item->no ?>" class="btn btn-call btn-default btn-sm"><i class="fa fa-star-o star primary"></i></button>
                                        <? } else { ?>
                                            <button id="fleet_star_<?= $item->no ?>" class="btn btn-call btn-default btn-sm"><i class="fa fa-star star primary"></i></button>
                                        <? } ?>
                                    </td>
                                    <td><a href="/fleet/?phase=<?= $item->phase ?>"><?= $item->phase ?></a></td>
                                    <td><a href="/fleet/item/<?= $item->no ?>"><?= $item->fleet ?></a></td>
                                    <td><?= @$item->instance->type ?></td>
                                    <td><?= $item->apps ?></td>
                                    <td><?= $item->servers ? $item->servers : 0 ?></td>
                                    <td>
                                        <button id="fleet_remove_<?= $item->no ?>" onclick="fleet_remove(this.id)" class="btn btn-default btn-sm"><i class="fa fa-trash primary"></i></button>
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

<div id="md-fleet-remove" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">경고</h4>
            </div>
            <div id="md-fleet-remove-msg" class="modal-body">
                <h4>정말 Fleet 를 삭제 할까요?</h4>
                <p>삭제 작업이 시작되면 되돌릴 수 없습니다.<br>설정에 저장된 모든 정보가 삭제됩니다.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <span id="md-fleet-remove-btn"><button type="button" class="btn btn-danger">Remove</button></span>
            </div>
        </div>
    </div>
</div>

<script src="/static/js/fleet.js?<?= VERSION ?>"></script>
