<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Project</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Project List (<?= count($list) ?>)</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th></th>
                                <th>job name</th>
                                <th>artifact id</th>
                                <th>version</th>
                                <th>type</th>
                                <th>modified</th>
                                <th>
                                    <a href="/project/item" title="new project" data-toggle="tooltip" data-placement="top" class="btn btn-form btn-default btn-sm"><i class="fa fa-plus primary"></i></a>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($list as $item) { ?>
                                <tr id="project-<?= $item->no ?>">
                                    <td>
                                        <? if (empty($item->u_no)) { ?>
                                            <button id="project_star_<?= $item->no ?>" class="btn btn-call btn-default btn-sm"><i class="fa fa-star-o star primary"></i></button>
                                        <? } else { ?>
                                            <button id="project_star_<?= $item->no ?>" class="btn btn-call btn-default btn-sm"><i class="fa fa-star star primary"></i></button>
                                        <? } ?>
                                    </td>
                                    <td><a href="/project/item/<?= $item->no ?>"><?= $item->name ?></a></td>
                                    <td><a href="/project/item/<?= $item->no ?>"><?= $item->artifactId ?></a></td>
                                    <td><?= $item->major ?>.<?= $item->minor ?>.<?= $item->build ?></td>
                                    <td><a href="/project/?p=<?= $item->packaging ?>"><?= $item->packaging ?></a></td>
                                    <td><?= $item->mod_date ?></td>
                                    <td>
                                        <button id="project_remove_<?= $item->no ?>" onclick="project_remove(this.id)" class="btn btn-default btn-sm"><i class="fa fa-trash primary"></i></button>
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

<div id="md-project-remove" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">경고</h4>
            </div>
            <div id="md-project-remove-msg" class="modal-body">
                <h4>정말 Project 를 삭제 할까요?</h4>
                <p>삭제 작업이 시작되면 되돌릴 수 없습니다.<br>설정에 저장된 모든 정보가 삭제됩니다.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <span id="md-project-remove-btn"><button type="button" class="btn btn-danger">Remove</button></span>
            </div>
        </div>
    </div>
</div>

<script src="/static/js/project.js?<?= VERSION ?>"></script>
