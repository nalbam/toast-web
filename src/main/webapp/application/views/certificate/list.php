<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Certificate</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Certificate List (<?= count($list) ?>)</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>name</th>
                                <th>
                                    <a href="/certificate/item" title="new certificate" data-toggle="tooltip" data-placement="top" class="btn btn-form btn-default btn-sm"><i class="fa fa-plus primary"></i></a>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($list as $item) { ?>
                                <tr>
                                    <td><a href="/certificate/item/<?= $item->no ?>"><?= $item->name ?></a></td>
                                    <td></td>
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
