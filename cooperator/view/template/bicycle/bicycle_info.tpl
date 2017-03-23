<?php echo $header; ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>单车详情<small>单车详情</small></h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i>首页</li>
        <li>单车详情</li>
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
                            <label for="" class="col-sm-4 control-label">单车编码</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['bicycle_sn']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">单车类型</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['type']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">锁编号</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['lock_sn']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">状态</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['is_using']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-12 padding">
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
<script type="text/javascript" src="http://cache.amap.com/lbs/static/addToolbar.js"></script>
<script type="text/javascript">
    var lnglat = [<?php echo $data['lng']; ?>, <?php echo $data['lat']; ?>];
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
