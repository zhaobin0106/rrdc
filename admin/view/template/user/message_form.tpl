<?php echo $header; ?>
<!-- Content Header (Page header) -->
<section class="content-header clearfix">
    <h1 class="pull-left">
        <span>系统消息</span>
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
<form class="form-horizontal" method="post" action="<?php echo $action; ?>">
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
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">标题</label>
                                <div class="col-sm-5">
                                    <input type="text" name="msg_title" value="<?php echo $data['msg_title']; ?>" class="form-control">
                                    <?php if (isset($error['msg_title'])) { ?><div class="text-danger"><?php echo $error['msg_title']; ?></div><?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">消息图片</label>
                                <div class="col-sm-5">
                                    <button type="button" class="img-thumbnail button-upload" style="outline: none;" data-tag="logo" data-action="<?php echo $upload_action; ?>" data-tage="image">
                                        <img src="<?php echo $data['msg_image_url']; ?>" alt="消息图片" style="max-width: 100px; max-height: 100px;" class="imageurl">
                                        <input type="hidden" name="msg_image" value="<?php echo $data['msg_image']; ?>" placeholder="消息图片" class="filepath">
                                    </button>
                                    <?php if (isset($error['msg_image'])) { ?><div class="text-danger"><?php echo $error['msg_image']; ?></div><?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">用户</label>
                                <div class="col-sm-5">
                                    <label class="margin-r-5">
                                        <input type="radio" name="user_type" value="0" class="button-all-users" <?php echo $data['user_type']==0 ? 'checked' : ''; ?> />
                                        全部用户
                                    </label>
                                    <label>
                                        <input type="radio" name="user_type" value="1" class="button-choose-users" <?php echo $data['user_type']==1 ? 'checked' : ''; ?> />
                                        自定义
                                    </label>&nbsp;
                                    <span class="choose-users-num"></span>
                                    <?php if (isset($error['mobiles'])) { ?><div class="text-danger"><?php echo $error['mobiles']; ?></div><?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">摘要</label>
                                <div class="col-sm-5">
                                    <textarea name="msg_abstract" class="form-control" rows="3"><?php echo $data['msg_abstract']; ?></textarea>
                                    <?php if (isset($error['msg_abstract'])) { ?><div class="text-danger"><?php echo $error['msg_abstract']; ?></div><?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">外部链接</label>
                                <div class="col-sm-5">
                                    <input type="text" name="msg_link" value="<?php echo $data['msg_link']; ?>" class="form-control">
                                    <?php if (isset($error['msg_link'])) { ?><div class="text-danger"><?php echo $error['msg_link']; ?></div><?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">正文</label>
                                <div class="col-sm-5">
                                    <div class="margin-bottom custom_content">
                                        <textarea name="msg_content" class="form-control" rows="5"><?php echo $data['msg_content']; ?></textarea>
                                    </div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">群发用户</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">搜索</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control button-search-user">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">用户</label>
                                <div class="col-sm-5">
                                    <ul style="max-height: 150px;overflow-y: auto;list-style-type: none;padding: 0;">
                                        <li class="col-sm-12 no-padding"><label><input class="check_all" type="checkbox" value="0">&nbsp;<span>全选</span></label></li>
                                        <?php if (is_array($users) && !empty($users)) { ?>
                                        <?php foreach($users as $user) { ?>
                                        <li class="col-sm-12 no-padding"><label><input type="checkbox" name="user[]" value="<?php echo $user['user_id']; ?>" class="check_item">&nbsp;<span><?php echo $user['mobile']; ?></span></label></li>
                                        <?php } ?>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default margin-r-5 button-cancel">取消</button>
                    <button type="button" class="btn btn-primary button-confirm">确定</button>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<script type="text/javascript">
    $(function () {
        // 选择用户类型
        $(".user-type button").click(function () {
            $(this).addClass("active");
        });
        /**
         * 自定义用户对象
         */
        $(".button-choose-users").click(function(){
            $(".modal").modal('show');
        });
        // 确定按钮
        $('.button-confirm').click(function() {
            $('.choose-users-num').text('已选中'+ $(".check_item:checked").length +'个用户');
            $(".modal").modal('hide');
        });
        // 取消按钮
        $('.button-cancel').click(function() {
            $(".modal").modal('hide');
        });
        // 全选
        $(".check_all").change(function () {
            $(".check_item").prop("checked", $(this).is(":checked"));
        });
        // 搜索用户
        $(".button-search-user").keyup(function () {
            var searchText = $(this).val();
            if (searchText) {
                var items = $(".check_item");
                items.parents("li").hide();
                items.each(function(i, e){
                    if ($(e).next("span").text().indexOf(searchText) != -1) {
                        $(e).parents("li").show();
                    }
                });
            } else {
                items.parents("li").show();
            }
        });
    })
</script>

<?php echo $footer;?>