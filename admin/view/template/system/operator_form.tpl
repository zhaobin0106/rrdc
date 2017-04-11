<?php echo $header; ?>
<!-- Content Header (Page header) -->
<section class="content-header clearfix">
    <h1 class="pull-left">
        <span><?php echo $languages['yysz'];?></span>
        <a href="javascript:;" onclick="collect('<?php echo $menu_id ?>',this)"><i class="<?php echo $menu_collect_status == 1? 'fa fa-star no-margin text-yellow' : 'fa fa-star-o text-gray'; ?>"></i></a>
    </h1>
    <div class="pull-right">
        <div class="pull-left" style="margin-right: 20px;">
            <i class="fa fa-bicycle"></i>
            <span><?php echo $languages['zongshu'];?><?php echo $total_bicycle; ?><?php echo $languages['tai'];?></span>
        </div>
        <div class="pull-left" style="margin-right: 20px;">
            <span><?php echo $languages['shiyongzhong'];?><?php echo $using_bicycle; ?><?php echo $languages['tai'];?></span>
        </div>
        <div class="pull-left" style="margin-right: 20px;">
            <span><?php echo $languages['guzhang'];?><?php echo $fault_bicycle; ?><?php echo $languages['tai'];?></span>
        </div>
    </div>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <!-- tab 标签 -->
                <ul class="nav nav-tabs">
                    <li class="active"><a href="javascript:;" data-toggle="tab"><?php echo $title; ?></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="bicycle">
                        <?php if (isset($error['warning'])) { ?>
                        <div class="alert alert-danger" style="opacity: 0.8;"><i class="fa fa-exclamation-circle"></i>&nbsp;<span><?php echo $error['warning']; ?></span>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <?php } ?>
                        <form class="form-horizontal" method="post" action="<?php echo $action; ?>">
                            <div class="row">
                                <div class="form-group required">
                                    <label class="col-sm-2 control-label" for="input-deposit"><?php echo $languages['yjy'];?></label>
                                    <div class="col-sm-5">
                                        <input type="text" name="config_operator_deposit" value="<?php echo $data['config_operator_deposit']; ?>" placeholder="<?php echo $languages['yjy'];?>" id="input-deposit" class="form-control" />
                                        <?php if (isset($error['config_operator_deposit'])) { ?><div class="text-danger"><?php echo $error['config_operator_deposit']; ?></div><?php } ?>
                                        <small class="help-block"></small>
                                    </div>
                                </div>
                                <!-- 交易安全校验码（key） -->
                                <div class="form-group required">
                                    <label class="col-sm-2 control-label" for="input-wechat"><?php echo $languages['wxgzh'];?></label>
                                    <div class="col-sm-5">
                                        <input type="text" name="config_wechat" value="<?php echo $data['config_wechat']; ?>" placeholder="微信公众号" id="input-wechat" class="form-control" />
                                        <?php if (isset($error['config_wechat'])) { ?><div class="text-danger"><?php echo $error['config_wechat']; ?></div><?php } ?>
                                        <small class="help-block"></small>
                                    </div>
                                </div>
                                <!-- 合作者身份（partner ID） -->
                                <div class="form-group required">
                                    <label class="col-sm-2 control-label" for="input-phone"><?php echo $languages['lxdh'];?></label>
                                    <div class="col-sm-5">
                                        <input type="text" name="config_phone" value="<?php echo $data['config_phone']; ?>" placeholder="<?php echo $languages['lxdh'];?>" id="input-phone" class="form-control" />
                                        <?php if (isset($error['config_phone'])) { ?><div class="text-danger"><?php echo $error['config_phone']; ?></div><?php } ?>
                                        <small class="help-block"></small>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <label class="col-sm-2 control-label" for="input-email">e-mail</label>
                                    <div class="col-sm-5">
                                        <input type="text" name="config_email" value="<?php echo $data['config_email']; ?>" placeholder="e-mail" id="input-email" class="form-control" />
                                        <?php if (isset($error['config_email'])) { ?><div class="text-danger"><?php echo $error['config_email']; ?></div><?php } ?>
                                        <small class="help-block"></small>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <label class="col-sm-2 control-label" for="input-web"><?php echo $languages['guanwang'];?></label>
                                    <div class="col-sm-5">
                                        <input type="text" name="config_web" value="<?php echo $data['config_web']; ?>" placeholder="<?php echo $languages['guanwang'];?>" id="input-web" class="form-control" />
                                        <?php if (isset($error['config_web'])) { ?><div class="text-danger"><?php echo $error['config_web']; ?></div><?php } ?>
                                        <small class="help-block"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-7">
                                    <button type="submit" class="btn btn-large btn-success pull-right"><?php echo $languages['tijiao'];?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php echo $footer;?>