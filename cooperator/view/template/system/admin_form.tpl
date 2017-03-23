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
                                <label class="col-sm-2 control-label">管理员名称</label>
                                <div class="col-sm-8">
                                    <?php if (empty($admin_id)) { ?>
                                    <input type="text" name="admin_name" value="<?php echo $data['admin_name']; ?>" class="form-control" />
                                    <?php if (isset($error['admin_name'])) { ?><div class="text-danger"><?php echo $error['admin_name']; ?></div><?php } ?>
                                    <?php } else { ?>
                                    <span><?php echo $data['admin_name']; ?></span>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">角色</label>
                                <div class="col-sm-8">
                                    <select name="role_id" class="form-control">
                                        <?php foreach($roles as $k => $v) { ?>
                                        <option value="<?php echo $k; ?>" <?php echo (string)$k == $data['role_id'] ? 'selected' : ''; ?>><?php echo $v; ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php if (isset($error['password'])) { ?><div class="text-danger"><?php echo $error['password']; ?></div><?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">密码</label>
                                <div class="col-sm-8">
                                    <input type="password" name="password" class="form-control"  placeholder="不修改密码时可不填" />
                                    <?php if (isset($error['password'])) { ?><div class="text-danger"><?php echo $error['password']; ?></div><?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">重复密码</label>
                                <div class="col-sm-8">
                                    <input type="password" name="confirm" class="form-control"  placeholder="不修改密码时可不填" />
                                    <?php if (isset($error['confirm'])) { ?><div class="text-danger"><?php echo $error['confirm']; ?></div><?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">状态</label>
                                <div class="col-sm-8">
                                    <input type="checkbox" name="state" value="1" placeholder="状态" class="bootstrap-switch in-list" data-on-text="启用" data-off-text="停用" data-label-width="5" <?php echo $data['state']==1 ? 'checked' : ''; ?> />
                                    <?php if (isset($error['state'])) { ?><div class="text-danger"><?php echo $error['state']; ?></div><?php } ?>
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
<link rel="stylesheet" href="<?php echo $static . "AdminLTE-2.3.7/";?>plugins/bootstrap-switch/bootstrap-switch.min.css" />
<script type="text/javascript" src="<?php echo $static . "AdminLTE-2.3.7/";?>plugins/bootstrap-switch/bootstrap-switch.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('input.bootstrap-switch').bootstrapSwitch();
    });
</script>

<?php echo $footer;?>