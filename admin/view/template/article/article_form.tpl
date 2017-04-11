<?php echo $header; ?>
<!-- Content Header (Page header) -->
<section class="content-header clearfix">
    <h1 class="pull-left">
        <span><?php echo $languages['wzgl'];?></span>
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
                                    <label class="col-sm-2 control-label"><?php echo $languages['wzbt'];?></label>
                                    <div class="col-sm-5">
                                        <input type="text" name="article_title" value="<?php echo $data['article_title']; ?>" class="form-control" />
                                        <?php if (isset($error['article_title'])) { ?><div class="text-danger"><?php echo $error['article_title']; ?></div><?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><?php echo $languages['wzfl'];?></label>
                                    <div class="col-sm-5">
                                        <select name="category_id" class="form-control">
                                            <?php foreach($categories as $k => $v) { ?>
                                            <option value="<?php echo $k; ?>" <?php if ((string)$k == $data['category_id']) { ?>selected<?php } ?>><?php echo $v; ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php if (isset($error['category_id'])) { ?><div class="text-danger"><?php echo $error['category_id']; ?></div><?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><?php echo $languages['wzgjz'];?></label>
                                    <div class="col-sm-5">
                                        <input type="text" name="article_code" value="<?php echo $data['article_code']; ?>" class="form-control">
                                        <?php if (isset($error['article_code'])) { ?><div class="text-danger"><?php echo $error['article_code']; ?></div><?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><?php echo $languages['wzpx'];?></label>
                                    <div class="col-sm-5">
                                        <input type="text" name="article_sort" value="<?php echo $data['article_sort']; ?>" class="form-control">
                                        <?php if (isset($error['article_sort'])) { ?><div class="text-danger"><?php echo $error['article_sort']; ?></div><?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><?php echo $languages['wznr'];?></label>
                                    <div class="col-sm-5">
                                        <textarea name="article_content" class="form-control" rows="5"><?php echo $data['article_content']; ?></textarea>
                                        <?php if (isset($error['article_content'])) { ?><div class="text-danger"><?php echo $error['article_content']; ?></div><?php } ?>
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
<?php echo $footer;?>