<?php echo $header; ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><?php echo $title; ?><small><?php echo $title; ?></small></h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i>首页</li>
        <li><?php echo $title; ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border"></div>
                <div class="box-body">
                    <?php if (isset($error['warning'])) { ?>
                    <div class="alert alert-danger" style="opacity: 0.8;"><i class="fa fa-exclamation-circle"></i>&nbsp;<span><?php echo $error['warning']; ?></span>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <?php } ?>
                    <form class="form-horizontal" method="post" action="<?php echo $action; ?>">
                        <div class="row">
                            <!-- ACCOUNT SID -->
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="input-account-sid">ACCOUNT SID</label>
                                <div class="col-sm-8">
                                    <input type="text" name="config_sms_account_sid" value="<?php echo $data['config_sms_account_sid']; ?>" placeholder="ACCOUNT SID" id="input-account-sid" class="form-control" />
                                    <?php if (isset($error['config_sms_account_sid'])) { ?><div class="text-danger"><?php echo $error['config_sms_account_sid']; ?></div><?php } ?>
                                </div>
                            </div>
                            <!-- AUTH TOKEN -->
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="input-account-token">AUTH TOKEN</label>
                                <div class="col-sm-8">
                                    <input type="text" name="config_sms_account_token" value="<?php echo $data['config_sms_account_token']; ?>" placeholder="AUTH TOKEN" id="input-account-token" class="form-control" />
                                    <?php if (isset($error['config_sms_account_token'])) { ?><div class="text-danger"><?php echo $error['config_sms_account_token']; ?></div><?php } ?>
                                </div>
                            </div>
                            <!-- APP ID -->
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="input-app-id">APP ID</label>
                                <div class="col-sm-8">
                                    <input type="text" name="config_sms_app_id" value="<?php echo $data['config_sms_app_id']; ?>" placeholder="APP ID" id="input-app-id" class="form-control" />
                                    <?php if (isset($error['config_sms_app_id'])) { ?><div class="text-danger"><?php echo $error['config_sms_app_id']; ?></div><?php } ?>
                                </div>
                            </div>
                            <!-- 模板ID -->
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="input-temp-id">模板 ID</label>
                                <div class="col-sm-8">
                                    <input type="text" name="config_sms_temp_id" value="<?php echo $data['config_sms_temp_id']; ?>" placeholder="模板 ID" id="input-temp-id" class="form-control" />
                                    <?php if (isset($error['config_sms_temp_id'])) { ?><div class="text-danger"><?php echo $error['config_sms_temp_id']; ?></div><?php } ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-large btn-success pull-right">提交</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php echo $footer;?>