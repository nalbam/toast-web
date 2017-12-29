<!-- page content -->
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Auth</h3>
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
                        <form id="user_save" data-parsley-validate class="form-horizontal form-label-left">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-3" for="org">org</label>
                                <div class="col-md-8 col-sm-8 col-xs-8">
                                    <label class="control-label"><?= @$org->id ?></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-3" for="provider">provider</label>
                                <div class="col-md-8 col-sm-8 col-xs-8">
                                    <label class="control-label"><?= @$user->provider ?></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-3" for="memberNo">memberNo</label>
                                <div class="col-md-8 col-sm-8 col-xs-8">
                                    <label class="control-label"><?= @$user->memberNo ?></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-3" for="username">username</label>
                                <div class="col-md-8 col-sm-8 col-xs-8">
                                    <label class="control-label"><?= @$user->username ?></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-3" for="nickname">nickname</label>
                                <div class="col-md-8 col-sm-8 col-xs-8">
                                    <label class="control-label"><?= @$user->nickname ?></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-3" for="phoneNum">phoneNum</label>
                                <div class="col-md-8 col-sm-8 col-xs-8">
                                    <label class="control-label"><input type="text" name="phoneNum" value="<?= @$user->phoneNum ?>" onchange="user_phone(this)" class="form-control input-sm" placeholder="01012345678"/></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-3" for="auth">auth</label>
                                <div class="col-md-8 col-sm-8 col-xs-8">
                                    <label class="control-label"><?= @$auth ? $auth : '권한이 없습니다.' ?></label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- /page content -->

<script>
    function user_phone(e) {
        let data = 'phone=' + e.value;
        ajax_call('/user/phone/', data, '');
    }
</script>
