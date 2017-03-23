<?php echo $header; ?>
<!-- Content Header (Page header) -->
<section class="content-header clearfix">
    <h1 class="pull-left">
        <span>单车管理</span>
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
                                    <label class="col-sm-2 control-label">单车数量</label>
                                    <div class="col-sm-5">
                                        <input type="text" name="bicycle_num" value="<?php echo $data['bicycle_num']; ?>" class="form-control" />
                                        <?php if (isset($error['bicycle_num'])) { ?><div class="text-danger"><?php echo $error['bicycle_num']; ?></div><?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">单车类型</label>
                                    <div class="col-sm-5">
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
                                    <div class="col-sm-5">
                                        <select name="region_id" class="form-control">
                                            <?php foreach($regions as $v) { ?>
                                            <option value="<?php echo $v['region_id']; ?>" <?php if ((string)$v['region_id'] == $data['region_id']) { ?>selected<?php } ?>><?php echo $v['region_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php if (isset($error['region_id'])) { ?><div class="text-danger"><?php echo $error['region_id']; ?></div><?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-7">
                                    <div class="pull-right">
                                        <button type="submit" class="btn btn-sm btn-success margin-r-5">提交</button>
                                        <a href="<?php echo $return_action; ?>" class="btn btn-sm btn-default">返回</a>
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
<?php if (isset($bicycles) && !empty($bicycles) && is_array($bicycles)) { ?>
<div class="col-xs-12">
    <div class="box no-border">
        <div class="box-body">
            <div class="form-group">
                <button class="btn btn-primary btn-sm" form="table_form" formaction="<?php echo $export_qrcode_action; ?>"><i class="fa fa-qrcode"></i>&nbsp;导出二维码</button>
            </div>
            <form id="table_form" class="table_form" method="post">
            <table class="table table-bordered table-hover dataTable" role="grid">
                <thead>
                <tr>
                    <th>单车编号</th>
                    <th>单车类型</th>
                    <th>景区</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($bicycles as $bicycle) { ?>
                <tr>
                    <td><input type="hidden" name="selected[]" value="<?php echo $bicycle['bicycle_id']; ?>" /><?php echo $bicycle['bicycle_sn']; ?></td>
                    <td><?php echo $bicycle['type_name']; ?></td>
                    <td><?php echo $bicycle['region_name']; ?></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
            </form>
        </div>
    </div>
</div>
<?php } ?>
<?php echo $footer;?>