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
                        <h2>Info</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <form id="form_certificate_save" action="/certificate/save" onsubmit="return false;">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>key</th>
                                    <th>value</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>name</td>
                                    <td><input type="text" id="name" name="name" class="form-control input-sm" value="<?= @$certificate->name ?>"></td>
                                </tr>
                                <tr>
                                    <td>memo</td>
                                    <td><textarea id="memo" name="memo" rows="3" class="form-control input-sm"><?= @$certificate->memo ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>certificate</td>
                                    <td><textarea id="certificate" name="certificate" rows="6" class="form-control input-sm"><?= @$certificate->certificate ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>certificate_key</td>
                                    <td><textarea id="certificate_key" name="certificate_key" rows="6" class="form-control input-sm"><?= @$certificate->certificate_key ?></textarea></td>
                                </tr>
                                <tr>
                                    <td>client_certificate</td>
                                    <td><textarea id="client_certificate" name="client_certificate" rows="6" class="form-control input-sm"><?= @$certificate->client_certificate ?></textarea></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <button id="certificate_save" class="btn btn-form btn-default btn-sm"><i class="fa fa-floppy-o primary"></i></button>
                                        <input type="hidden" name="no" value="<?= @$certificate->no ?>">
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
