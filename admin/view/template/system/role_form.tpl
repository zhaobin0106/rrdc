<?php echo $header; ?>
<!-- Content Header (Page header) -->
<section class="content-header clearfix">
    <h1 class="pull-left">
        <span><?php echo $languages['jsqx'];?></span>
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
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><?php echo $languages['jsm'];?></label>
                                    <div class="col-sm-5">
                                        <input type="text" name="role_name" value="<?php echo $data['role_name']; ?>" class="form-control" />
                                        <?php if (isset($error['role_name'])) { ?><div class="text-danger"><?php echo $error['role_name']; ?></div><?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><?php echo $languages['quanxian'];?></label>
                                    <div class="col-sm-5">
                                        <input type="hidden" name="role_permission" value='<?php echo $role_permission; ?>' />
                                        <ul id="permission-tree" class="ztree"></ul>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><?php echo $languages['zhuangtai'];?></label>
                                    <div class="col-sm-5">
                                        <input type="checkbox" name="state" value="1" placeholder="<?php echo $languages['zhuangtai'];?>" class="bootstrap-switch in-list" data-on-text="<?php echo $languages['qiyong'];?>" data-off-text="<?php echo $languages['tingyong'];?>" data-label-width="5" <?php echo $data['state']==1 ? 'checked' : ''; ?> />
                                        <?php if (isset($error['state'])) { ?><div class="text-danger"><?php echo $error['state']; ?></div><?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-7">
                                    <div class="pull-right">
                                        <button type="submit" class="btn btn-sm btn-success margin-r-5"><?php echo $languages['tijiao'];?></button>
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
<link rel="stylesheet" href="<?php echo $static . 'AdminLTE-2.3.7/';?>plugins/bootstrap-switch/bootstrap-switch.min.css" />
<script type="text/javascript" src="<?php echo $static . 'AdminLTE-2.3.7/'; ?>plugins/bootstrap-switch/bootstrap-switch.min.js"></script>
<script type="text/javascript" src="<?php echo $static . 'AdminLTE-2.3.7/'; ?>plugins/bootstrap-switch/bootstrap-switch.min.js"></script>

<link rel="stylesheet" href="<?php echo $static . 'AdminLTE-2.3.7/'; ?>plugins/jquery.treegrid/css/jquery.treegrid.css" />
<link rel="stylesheet" href="<?php echo $static . 'AdminLTE-2.3.7/'; ?>plugins/ztree/zTreeStyle/zTreeStyle.css" />
<script type="text/javascript" src="<?php echo $static . 'AdminLTE-2.3.7/'; ?>plugins/jquery.treegrid/js/jquery.treegrid.js"></script>
<script type="text/javascript" src="<?php echo $static . 'AdminLTE-2.3.7/'; ?>plugins/jquery.treegrid/js/jquery.treegrid.bootstrap3.js"></script>
<script type="text/javascript" src="<?php echo $static . 'AdminLTE-2.3.7/'; ?>plugins/ztree/jquery.ztree.core.js"></script>
<script type="text/javascript" src="<?php echo $static . 'AdminLTE-2.3.7/'; ?>plugins/ztree/jquery.ztree.excheck.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('input.bootstrap-switch').bootstrapSwitch();
        var setting = {
            check: {
                enable: true
            },
            callback: {
                onCheck: updatePermission
            },
            data: {
                simpleData: {
                    enable: true
                }
            }
        };
        var permissions = <?php echo $select_permission; ?>;
        $.fn.zTree.init($("#permission-tree"), setting, permissions);
    });

    function updatePermission(event, treeId, treeNode) {
        var tree = $('#' + treeId),
                role_id = tree.data('role_id'),
                checkedNodes = $.fn.zTree.getZTreeObj(treeId).getCheckedNodes(true),
                theButton = tree.parents('.dropdown').children('button'),
                checkedIds = [];

        for(var i in checkedNodes) {
            checkedIds.push(checkedNodes[i].id);
        }

        $('[name="role_permission"]').val(JSON.stringify(checkedIds));
    }
</script>
<?php echo $footer;?>