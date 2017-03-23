<?php echo $header; ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>锁详情<small>锁详情</small></h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i>首页</li>
        <li>锁详情</li>
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
                            <label for="" class="col-sm-4 control-label">锁编号</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['lock_sn']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">锁名称</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['lock_name']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">电池电压(伏特)</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['gx']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">充电电压(伏特)</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['gy']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">当前电量（百分比）</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['battery']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">信号强度(dB)</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['gz']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">开锁次数</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['open_nums']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">更新时间</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['system_time']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">状态</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['lock_status']; ?></span>
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
