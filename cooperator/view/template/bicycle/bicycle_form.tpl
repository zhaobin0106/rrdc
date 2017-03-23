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
                                <label class="col-sm-2 control-label">单车编号</label>
                                <div class="col-sm-8">
                                    <input type="text" name="bicycle_sn" value="<?php echo $data['bicycle_sn']; ?>" class="form-control" />
                                    <?php if (isset($error['bicycle_sn'])) { ?><div class="text-danger"><?php echo $error['bicycle_sn']; ?></div><?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">单车类型</label>
                                <div class="col-sm-8">
                                    <select name="type" class="form-control">
                                        <?php foreach($types as $k => $v) { ?>
                                        <option value="<?php echo $k; ?>" <?php if ((string)$k == $data['type']) { ?>selected<?php } ?>><?php echo $v; ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php if (isset($error['type'])) { ?><div class="text-danger"><?php echo $error['type']; ?></div><?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">所在景区</label>
                                <div class="col-sm-8">
                                    <select name="type" class="form-control">
                                        <?php foreach($regions as $v) { ?>
                                        <option value="<?php echo $v['region_id']; ?>" <?php if ((string)$v['region_id'] == $data['region_id']) { ?>selected<?php } ?>><?php echo $v['region_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php if (isset($error['region_id'])) { ?><div class="text-danger"><?php echo $error['region_id']; ?></div><?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">车锁编号</label>
                                <div class="col-sm-8">
                                    <input type="text" name="lock_sn" value="<?php echo $data['lock_sn']; ?>" class="form-control">
                                    <?php if (isset($error['lock_sn'])) { ?><div class="text-danger"><?php echo $error['lock_sn']; ?></div><?php } ?>
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