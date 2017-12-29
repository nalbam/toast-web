<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>User : <?= $item->username ?></h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Auth</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <form id="form_user_save" action="/user/save/<?= $item->no ?>" onsubmit="return false;">

                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>read only</th>
                                    <th>build & deploy</th>
                                    <th>system</th>
                                    <th>admin</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <label for="auth_read" class="checkbox-inline">
                                            <input type="checkbox" id="auth_read" name="auth[]" value="read" <?= _contains($item->auth, 'read') ? 'checked="checked"' : '' ?>> read only
                                        </label>
                                    </td>
                                    <td>
                                        <? foreach ($phases as $phase) { ?>
                                            <label for="auth_<?= $phase->key ?>" class="checkbox-inline">
                                                <input type="checkbox" id="auth_<?= $phase->key ?>" name="auth[]" value="<?= $phase->key ?>" class="not_read_only" <?= _contains($item->auth, $phase->key) ? 'checked="checked"' : '' ?>> <?= $phase->key ?>
                                            </label>
                                        <? } ?>
                                    </td>
                                    <td>
                                        <label for="auth_system" class="checkbox-inline">
                                            <input type="checkbox" id="auth_system" name="auth[]" value="system" class="not_read_only" <?= _contains($item->auth, 'system') ? 'checked="checked"' : '' ?>> system
                                        </label>
                                    </td>
                                    <td>
                                        <label for="auth_admin" class="checkbox-inline">
                                            <input type="checkbox" id="auth_admin" name="auth[]" value="admin" class="not_read_only" <?= _contains($item->auth, 'admin') ? 'checked="checked"' : '' ?>> admin
                                        </label>
                                    </td>
                                    <td>
                                        <button id="user_save" class="btn btn-form btn-default btn-sm"><i class="fa fa-floppy-o primary"></i></button>
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

<script>
    $(function () {
        $("#auth_read").click(function () {
            let chk = $(this).is(":checked");
            console.log('this.id : ' + this.id + ' checked ' + chk);
            if (chk) {
                $(".not_read_only").prop('checked', false);
            }
        });
        $(".not_read_only").click(function () {
            let chk = $(this).is(":checked");
            console.log('this.id : ' + this.id + ' checked ' + chk);
            if (chk) {
                $("#auth_read").prop('checked', false);
            }
        });
    });
</script>
