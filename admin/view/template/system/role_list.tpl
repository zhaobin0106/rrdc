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
                    <li class="active"><a href="javascript:;" data-toggle="tab"><?php echo $languages['jslb'];?></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="bicycle">
                        <!-- 新增 -->
                        <div class="form-group">
                            <a href="<?php echo $add_action; ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i>&nbsp;<?php echo $languages['xinzeng'];?></a>
                            <!--<button class="btn btn-default btn-sm button-upload" data-action="<?php echo $import_action; ?>"><i class="fa fa-upload"></i>&nbsp;导入</button>
                            <button class="btn btn-default btn-sm" form="table_form" formaction="<?php echo $export_action; ?>"><i class="fa fa-download"></i>&nbsp;导出</button>-->
                        </div>
                        <?php if (isset($error['warning'])) { ?>
                        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>&nbsp;<?php echo $error['warning']; ?>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <?php } ?>
                        <?php if (isset($success)) { ?>
                        <div class="alert bg-light-blue"><i class="fa fa-check-circle"></i>&nbsp;<?php echo $success; ?>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <?php } ?>
                        <form id="table_form" class="table_form" method="post">
                            <table class="table table-bordered table-hover dataTable" role="grid">
                                <thead>
                                <tr>
                                    <!--<th style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']:enabled').prop('checked', this.checked);"></th>-->
                                    <?php foreach ($data_columns as $column) { ?>
                                    <th><?php echo $column['text']; ?></th>
                                    <?php } ?>
                                    <th style="min-width:130px;"><?php echo $languages['caozuo'];?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($data_rows as $data) { ?>
                                <tr>
                                    <!--<td><input type="checkbox" name="selected[]" value="<?php echo $data['bicycle_id']?>"></td>-->
                                    <td><?php echo $data['role_name']?></td>
                                    <td><?php echo $data['state']?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button data-url="<?php echo $data['edit_action']; ?>" type="button" class="btn btn-info link"><i class="fa fa-fw fa-edit"></i><?php echo $languages['bianji'];?></button>
                                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="<?php echo $data['delete_action']; ?>"><?php echo $languages['shanchu'];?></a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </form>
                        <div class="row"><div class="col-sm-6 text-left"><?php echo $pagination; ?></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

<script type="text/javascript">
    $('.date-range').daterangepicker({
        locale:{
            format: 'YYYY-MM-DD',
            isAutoVal:false,
        }
    });

    $("#filter_type").change(function() {
        $("#filter_text").attr("name", $(this).val());
    });
</script>
<?php echo $footer;?>