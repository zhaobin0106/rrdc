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
                            <!-- 商户编号 -->
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="input-user-code">商户编号</label>
                                <div class="col-sm-8">
                                    <input type="text" name="config_yin_han_user_code" value="<?php echo $data['config_yin_han_user_code']; ?>" placeholder="商户编号" id="input-user-code" class="form-control" />
                                    <?php if (isset($error['config_yin_han_user_code'])) { ?><div class="text-danger"><?php echo $error['config_yin_han_user_code']; ?></div><?php } ?>
                                </div>
                            </div>
                            <!-- key -->
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="input-des-key">KEY</label>
                                <div class="col-sm-8">
                                    <input type="text" name="config_yin_han_des_key" value="<?php echo $data['config_yin_han_des_key']; ?>" placeholder="KEY" id="input-des-key" class="form-control" />
                                    <?php if (isset($error['config_yin_han_des_key'])) { ?><div class="text-danger"><?php echo $error['config_yin_han_des_key']; ?></div><?php } ?>
                                </div>
                            </div>
                            <!-- 应用编号 -->
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="input-sys-code">应用编号</label>
                                <div class="col-sm-8">
                                    <input type="text" name="config_yin_han_sys_code" value="<?php echo $data['config_yin_han_sys_code']; ?>" placeholder="合作者身份（partner ID）" id="input-sys-code" class="form-control" />
                                    <?php if (isset($error['config_yin_han_sys_code'])) { ?><div class="text-danger"><?php echo $error['config_yin_han_sys_code']; ?></div><?php } ?>
                                </div>
                            </div>
                            <!-- 接口地址 -->
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="input-api-url">接口地址</label>
                                <div class="col-sm-8">
                                    <input type="text" name="config_yin_han_api_url" value="<?php echo $data['config_yin_han_api_url']; ?>" placeholder="接口地址" id="input-api-url" class="form-control" />
                                    <?php if (isset($error['config_yin_han_api_url'])) { ?><div class="text-danger"><?php echo $error['config_yin_han_api_url']; ?></div><?php } ?>
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