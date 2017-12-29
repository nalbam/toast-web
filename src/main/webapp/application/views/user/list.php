<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>User</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>User List (<?= count($list) ?>)</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>provider</th>
                                <th>memberNo</th>
                                <th>username</th>
                                <th>nickname</th>
                                <th>phoneNum</th>
                                <th>auth</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($list as $item) { ?>
                                <tr id="user-<?= $item->no ?>">
                                    <td><img src="/static/img/<?= $item->provider ?>.png" width="24" height="24"></td>
                                    <td><?= $item->memberNo ?></td>
                                    <td><?= $item->username ?></td>
                                    <td><?= $item->nickname ?></td>
                                    <td><?= _phone_mask($item->phoneNum) ?></td>
                                    <td><a href="/user/item/<?= $item->no ?>"><?= $item->auth ? $item->auth : 'none' ?></a></td>
                                    <td>
                                        <button id="user_remove_<?= $item->no ?>" class="btn btn-call btn-default btn-sm"><i class="fa fa-trash primary"></i></button>
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
