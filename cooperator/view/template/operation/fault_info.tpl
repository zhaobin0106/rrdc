<?php echo $header; ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>故障详情<small>故障详情</small></h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i>首页</li>
        <li>故障详情</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border"></div>
                <div class="box-body">
                    <form class="form-horizontal" method="post" action="http://admin.estaxi.app.estronger.cn/orders/Index/carpool_order_save">

                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">单车编号</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['bicycle_sn']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">锁编号</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['lock_sn']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">故障类型</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['fault_type']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">用户名</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['user_name']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">故障描述</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['fault_content']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">上报时间</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['add_time']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="" class="col-sm-2 control-label">上报位置</label>
                            <div class="col-sm-12" style="height: 400px;box-sizing: content-box;">
                                <div id="container"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<link rel="stylesheet" href="http://cache.amap.com/lbs/static/main1119.css"/>
<script type="text/javascript"  src="http://webapi.amap.com/maps?v=1.3&key=38c88d25e4aa2652bc7806db2d1f6a0d&plugin=AMap.Geocoder"></script>
<script src="<?php echo HTTP_CATALOG;?>js/coordinate.js"></script>
<script type="text/javascript">
    var lnglat = wgs84togcj02(parseFloat(<?php echo $data['lng']; ?>), parseFloat(<?php echo $data['lat']; ?>));
    var marker, map = new AMap.Map("container", {
        resizeEnable: true,
        center: lnglat,
        zoom: 13
    });
    marker = new AMap.Marker({
        position: lnglat,
        map: map
    });
</script>
<?php echo $footer;?>