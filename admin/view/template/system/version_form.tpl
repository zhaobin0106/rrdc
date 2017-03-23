<?php echo $header; ?>
<!-- Content Header (Page header) -->

<section class="content-header clearfix">
    <h1 class="pull-left">
        <span>版本管理</span>
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
                        <div class="form-horizontal">
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">类型</label>
                                    <div class="btn-group col-sm-5">
                                        <button type="button" class="btn btn-default select-ios active">IOS</button>
                                        <button type="button" class="btn btn-default select-android">安卓</button>
                                    </div>
                                </div>
                             </div>
                         </div>
                        <form class="form-horizontal" method="post" action="<?php echo $action; ?>">
                            <input class="version version-type" type="hidden" name="type" value="<?php echo !empty($data['type']) ? $data['type'] : '1'; ?>">
                            <div class="row">
                                <div class="form-group required version-code-ios">
                                    <label class="col-sm-2 control-label" for="input-version-code">版本号</label>
                                    <div class="col-sm-5">
                                        <input type="text" value="<?php echo $data['type'] == 1 ? $data['version_code'] : ''; ?>" placeholder="版本号" id="input-version-code" class="form-control" onkeyup="versionCode(this)">
                                    </div>
                                </div>
                                <div class="form-group version-code-android" style="display:none;">
                                    <label class="col-sm-2 control-label">文件</label>
                                    <div class="col-sm-5">
                                        <button type="button" class="btn btn-primary btn-sm button-upload" style="outline: none;" data-tag="app" data-action="<?php echo $upload_url; ?>" data-tage="app">
                                            <i class="fa fa-upload"></i>
                                            <div class="inline">上传（当前版本：<span class="no-padding version_name"><?php echo $data['version_name']; ?></span>[<span class="no-padding version_code"><?php echo $data['version_code']; ?></span>]）</div>
                                            <input type="hidden" name="filepath" value="<?php echo $data['filepath']; ?>" class="filepath" />
                                            <input type="hidden" name="version_code" value="<?php echo $data['version_code']; ?>" class="version_code" />
                                            <input type="hidden" name="version_name" value="<?php echo $data['version_name']; ?>" class="version_name" />
                                        </button>
                                        <?php if (isset($error['filename'])) { ?><div class="text-danger"><?php echo $error['filename']; ?></div><?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">更新内容</label>
                                    <div class="col-sm-5">
                                        <textarea name="description" rows="5" class="form-control"><?php echo $data['description']; ?></textarea>
                                        <?php if (isset($error['description'])) { ?><div class="text-danger"><?php echo $error['description']; ?></div><?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">状态</label>
                                    <div class="col-sm-5">
                                        <input type="checkbox" name="state" value="1" placeholder="状态" class="bootstrap-switch in-list" data-on-text="启用" data-off-text="停用" data-label-width="5" <?php echo $data['state']==1 ? 'checked' : ''; ?> />
                                        <?php if (isset($error['state'])) { ?><div class="text-danger"><?php echo $error['state']; ?></div><?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">是否强制更新</label>
                                    <div class="col-sm-5">
                                        <input type="checkbox" name="forced_update" value="1" placeholder="状态" class="bootstrap-switch in-list" data-on-text="是" data-off-text="否" data-label-width="5" <?php echo $data['forced_update']==1 ? 'checked' : ''; ?> />
                                        <?php if (isset($error['forced_update'])) { ?><div class="text-danger"><?php echo $error['forced_update']; ?></div><?php } ?>
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
<link rel="stylesheet" href="<?php echo HTTP_CATALOG . "AdminLTE-2.3.7/";?>plugins/bootstrap-switch/bootstrap-switch.min.css" />
<script type="text/javascript" src="<?php echo HTTP_CATALOG . "AdminLTE-2.3.7/";?>plugins/bootstrap-switch/bootstrap-switch.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('input.bootstrap-switch').bootstrapSwitch();
});

if("<?php echo $data['type']; ?>" == 1){
    $(".version-type").val(1);
    $(".version-code-android").hide();
    $(".version-code-ios").show();
    $(".select-ios").addClass("active");
    $(".select-android").attr("disabled","disabled");
    $(".select-android").removeClass("active");
}else if("<?php echo $data['type']; ?>" == 2){
    $(".version-type").val(2);
    $(".version-code-ios").hide();
    $(".version-code-android").show();
    $(".select-android").addClass("active");
    $(".select-ios").attr("disabled","disabled");
    $(".select-ios").removeClass("active");
}
$(document).on('click','.select-ios',function(){
    $(".version-type").val(1);
    $(".version-code-android").hide();
    $(".version-code-ios").show();
    $(".select-ios").addClass("active");
    $(".select-android").removeClass("active");
});
$(document).on('click','.select-android',function(){
    $(".version-type").val(2);
    $(".version-code-ios").hide();
    $(".version-code-android").show();
    $(".select-android").addClass("active");
    $(".select-ios").removeClass("active");
});
</script>

<script>
    function versionCode(tag){
        $("[name = 'version_code']").val($(tag).val());
    }
</script>
<?php echo $footer;?>