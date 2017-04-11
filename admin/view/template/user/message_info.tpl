<?php echo $header; ?>
<!-- Content Header (Page header) -->
<section class="content-header clearfix">
    <h1 class="pull-left">
        <span><?php echo $languages['xtxx'];?></span>
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
                    <li class="active"><a href="javascript:;" data-toggle="tab">系统消息详情</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="bicycle">
                        <?php if (isset($error['warning'])) { ?>
                        <div class="alert alert-danger" style="opacity: 0.8;"><i class="fa fa-exclamation-circle"></i>&nbsp;<span><?php echo $error['warning']; ?></span>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <?php } ?>
                        <form class="form-horizontal" method="post">
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="" class="col-sm-2 control-label"><?php echo $languages['biaoti'];?></label>
                                    <div class="col-sm-5" style="margin-top: 7px;">
                                        <span><?php echo $data['msg_title']; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="" class="col-sm-2 control-label"><?php echo $languages['xxtp'];?></label>
                                    <div class="col-sm-5" style="margin-top: 7px;">
                                        <div class="img-thumbnail"><img src="<?php echo $data['msg_image_url']; ?>" style="max-width: 200px; max-height: 200px;" /></div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="" class="col-sm-2 control-label"><?php echo $languages['yonghu'];?></label>
                                    <div class="col-sm-5" style="margin-top: 7px;">
                                        <span><?php echo $data['user_name']; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="" class="col-sm-2 control-label"><?php echo $languages['zhaiyao'];?></label>
                                    <div class="col-sm-5" style="margin-top: 7px;">
                                        <span><?php echo $data['msg_abstract']; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="" class="col-sm-2 control-label"><?php echo $languages['wblj'];?></label>
                                    <div class="col-sm-5" style="margin-top: 7px;">
                                        <div style="word-break: break-all;"><a href="<?php echo $data['msg_link']; ?>" target="_blank"><?php echo $data['msg_link']; ?></a></div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label for="" class="col-sm-2 control-label"><?php echo $languages['zhengwen'];?></label>
                                    <div class="col-sm-5" style="margin-top: 7px;">
                                        <span><?php echo $data['msg_content']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-7">
                                    <div class="pull-right">
                                        <a href="<?php echo $return_action; ?>" class="btn btn-sm btn-default"><?php echo $languages['fanhui'];?></a>
                                    </div>
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
