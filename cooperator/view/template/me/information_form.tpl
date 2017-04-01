<?php echo $header; ?>
<!-- Content Header (Page header) -->
<section class="content-header clearfix">
    <h1 class="pull-left">
        <span>个人中心</span>
        <a href="javascript:;" onclick="collect('<?php echo $menu_id ?>',this)"><i class="<?php echo $menu_collect_status == 1? 'fa fa-star no-margin text-yellow' : 'fa fa-star-o text-gray'; ?>"></i></a>
    </h1>
    <div class="pull-right">
        <div class="pull-left" style="margin-right: 20px;">
            <i class="fa fa-bicycle"></i>
            <span>总数：<?php echo $total_bicycle; ?>台</span>
        </div>
        <div class="pull-left" style="margin-right: 20px;">
            <span>使用中：<?php echo $using_bicycle; ?>台</span>
        </div>
        <div class="pull-left" style="margin-right: 20px;">
            <span>故障：<?php echo $fault_bicycle; ?>台</span>
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
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">用户名</label>
                                    <div class="col-sm-5">
                                        <h5><?php echo $data['admin_name']; ?></h5>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">密码</label>
                                    <div class="col-sm-5">
                                        <input type="password" name="password" class="form-control"  placeholder="不修改密码时可不填" />
                                        <?php if (isset($error['password'])) { ?><div class="text-danger"><?php echo $error['password']; ?></div><?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">重复密码</label>
                                    <div class="col-sm-5">
                                        <input type="password" name="confirm" class="form-control"  placeholder="不修改密码时可不填" />
                                        <?php if (isset($error['confirm'])) { ?><div class="text-danger"><?php echo $error['confirm']; ?></div><?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">注册时间</label>
                                    <div class="col-sm-5">
                                        <h5><?php echo $data['add_time']; ?></h5>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-7">
                                    <div class="pull-right">
                                        <button type="submit" class="btn btn-sm btn-success margin-r-5">提交</button>
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