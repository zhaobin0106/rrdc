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
                            <div class="form-group">
                                <label class="col-sm-2 control-label">锁编号</label>
                                <div class="col-sm-8">
                                    <?php if (empty($lock_sn)) { ?>
                                    <input type="text" name="lock_sn" value="<?php echo $data['lock_sn']; ?>" class="form-control" />
                                    <?php if (isset($error['lock_sn'])) { ?><div class="text-danger"><?php echo $error['lock_sn']; ?></div><?php } ?>
                                    <?php } else { ?>
                                    <span><?php echo $data['lock_sn']; ?></span>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">锁名称</label>
                                <div class="col-sm-8">
                                    <input type="text" name="lock_name" value="<?php echo $data['lock_name']; ?>" class="form-control">
                                    <?php if (isset($error['lock_name'])) { ?><div class="text-danger"><?php echo $error['lock_name']; ?></div><?php } ?>
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