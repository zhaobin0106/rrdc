<?php echo $header; ?>
<!-- Content Header (Page header) -->
<section class="content-header clearfix">
    <h1 class="pull-left">
        <span>用户管理</span>
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
                    <li class="active"><a href="javascript:;" data-toggle="tab">用户信息详情</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="bicycle">
                        <?php if (isset($error['warning'])) { ?>
                        <div class="alert alert-danger" style="opacity: 0.8;"><i class="fa fa-exclamation-circle"></i>&nbsp;<span><?php echo $error['warning']; ?></span>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <?php } ?>
                        <form class="form-horizontal" method="post">
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="" class="col-sm-2 control-label">头像</label>
                                    <div class="col-sm-10">
                                        <?php if ($data['avatar']) { ?><span class="img-thumbnail"><img src="<?php echo $data['avatar']; ?>" style="max-width:100px;max-height:100px;" /></span><?php } ?>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="" class="col-sm-4 control-label">手机号码</label>
                                    <div class="col-sm-8">
                                        <span><?php echo $data['mobile']; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="" class="col-sm-4 control-label">用户昵称</label>
                                    <div class="col-sm-8">
                                        <span><?php echo $data['nickname']; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="" class="col-sm-4 control-label">押金(元)</label>
                                    <div class="col-sm-8">
                                        <span><?php echo $data['deposit']; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="" class="col-sm-4 control-label">可用金额(元)</label>
                                    <div class="col-sm-8">
                                        <span><?php echo $data['available_deposit']; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="" class="col-sm-4 control-label">信用积分</label>
                                    <div class="col-sm-8">
                                        <span><?php echo $data['credit_point']; ?></span>
                                    </div>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label for="" class="col-sm-4 control-label">实名认证状态</label>
                                    <div class="col-sm-8">
                                        <span><?php echo $verify_states[$data['verify_state']]; ?></span>
                                    </div>
                                </div>
                                <?php if ($data['verify_state'] ==1) { ?>
                                <div class="form-group col-sm-6">
                                    <label for="" class="col-sm-4 control-label">真实姓名</label>
                                    <div class="col-sm-8">
                                        <span><?php echo $data['real_name']; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="" class="col-sm-4 control-label">身份证</label>
                                    <div class="col-sm-8">
                                        <span><?php echo $data['identification']; ?></span>
                                    </div>
                                </div>
                                <?php } ?>

                                <div class="form-group col-sm-6">
                                    <label for="" class="col-sm-4 control-label">是否可踩车</label>
                                    <div class="col-sm-8">
                                        <span><?php echo $available_states[$data['available_state']]; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="" class="col-sm-4 control-label">最近登录时间</label>
                                    <div class="col-sm-8">
                                        <span><?php echo $data['login_time']; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="" class="col-sm-4 control-label">最近登录ip</label>
                                    <div class="col-sm-8">
                                        <span><?php echo $data['ip']; ?></span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="" class="col-sm-4 control-label">注册时间</label>
                                    <div class="col-sm-8">
                                        <span><?php echo $data['add_time']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="pull-right">
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
<link rel="stylesheet" href="http://cache.amap.com/lbs/static/main1119.css"/>
<script type="text/javascript"  src="http://webapi.amap.com/maps?v=1.3&key=38c88d25e4aa2652bc7806db2d1f6a0d&plugin=AMap.Geocoder&callback=initMap"></script>
<script src="<?php echo HTTP_CATALOG;?>js/coordinate.js"></script>
<script type="text/javascript">
    <?php if (isset($data['lng']) && isset($data['lat'])) { ?>
        var lnglat = wgs84togcj02(parseFloat(<?php echo $data['lng']; ?>), parseFloat(<?php echo $data['lat']; ?>));
    <?php } ?>

    var initMap = function(){
        if(typeof AMap != 'undefined') {
            var marker, map = new AMap.Map("container", {
                resizeEnable: true,
                zoom: 13
            });
            marker = new AMap.Marker({
                map: map
            });
            if (typeof lnglat != 'undefined') {
                marker.setPosition(lnglat);
                map.setCenter(lnglat);
            }
        }
    };
</script>
<?php echo $footer;?>
