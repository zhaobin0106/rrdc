<?php echo $header; ?>
<link rel="stylesheet" href="<?php echo HTTP_IMAGE; ?>css/plugins/bootstrap-treeview.min.css">
<link rel="stylesheet" href="<?php echo HTTP_IMAGE; ?>css/admin/index.css">
<link rel="stylesheet" href="<?php echo HTTP_IMAGE; ?>css/admin/magnific-popup.css">
<link rel="stylesheet" href="http://cache.amap.com/lbs/static/main1119.css"/>
<div id="admin-main">
    <div id="dashboard">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <h1 class="dashboard-title"><i class="fa fa-dashboard fa-fw"></i> 管理总控台<span> Dashboard</span></h1>
        </div>
        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
            <ul id="sparks" class="">
                <li class="sparks-info">
                    <h5> 注册用户数 <span class="txt-color-blue"><?php echo $user_sum['total'] ?></span></h5>
                    <div class="sparkline txt-color-blue hidden-mobile hidden-md hidden-sm hidden">
                        <?php echo implode(',', array_column($user_sum['list'], 'count'));?>
                    </div>
                </li>
                <li class="sparks-info">
                    <h5> 单车数 <span class="txt-color-purple"><?php echo $bicycle_sum['total'] ?></span></h5>
                    <div class="sparkline txt-color-purple hidden-mobile hidden-md hidden-sm hidden">
                        <?php echo implode(',', array_column($bicycle_sum['list'], 'count'));?>
                    </div>
                </li>
                <li class="sparks-info">
                    <h5> 使用中 <span class="txt-color-greenDark"><?php echo $used_sum['total'] ?></span></h5>
                    <div class="sparkline txt-color-greenDark hidden-mobile hidden-md hidden-sm hidden">
                        <?php echo implode(',', array_column($used_sum['list'], 'count'));?>
                    </div>
                </li>
                <li class="sparks-info">
                    <h5> 待维护 <span class="txt-color-orangeDark"><?php echo $fault_sum['total'] ?></span></h5>
                    <div class="sparkline txt-color-orangeDark hidden-mobile hidden-md hidden-sm hidden">
                        <?php echo implode(',', array_column($fault_sum['list'], 'count'));?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div id="dashboard-content">
        <div id="map-n-statistics" class="no-padding">
            <div id="map"></div>
            <div id="map-toolbar">
                <div class="dropdown place-select">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" style="font-size: 14px; vertical-align: baseline;">
                        西通电子
                        <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu" id="treeview"></div>
                </div>
                |
                <button type="button" class="btn btn-default btn-xs btn-no-border btn-refresh-marker" style="font-size: 14px; vertical-align: baseline;">
                    <i class="fa fa-refresh"></i> <span class="hidden-phone hidden-xs hidden-sm">刷新</span>
                </button>
                <button type="button" class="btn btn-default btn-xs btn-no-border btn-map-label" style="font-size: 14px; vertical-align: baseline;">
                    <i class="fa fa-map-pin"></i> <span class="hidden-phone hidden-xs hidden-sm">标注</span>
                </button>
                <div class="dropdown" style="margin-right: 0;">
                    <button class="btn btn-default btn-xs btn-no-border dropdown-toggle" type="button" data-toggle="dropdown" style="font-size: 14px; vertical-align: baseline;">
                        <i class="fa fa-cog"></i> 工具
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu tools-select" aria-labelledby="dropdownMenu1">
                        <li class="tool-ruler"><a href="#"><i class="fa fa-expand fa-fw"></i> 测距</a></li>
                        <li class="tool-share"><a href="#"><i class="fa fa-share-alt fa-fw"></i> 分享</a></li>
                    </ul>
                </div>
                <button type="button" class="btn btn-default btn-xs btn-no-border btn-map-maximize" style="font-size: 14px; vertical-align: baseline;">
                    <i class="fa fa-arrows-alt"></i> <span class="hidden-phone hidden-xs hidden-sm">全屏地图</span>
                </button>
                |
                <div class="checkbox" style="margin-left: 3px;">
                    <!--
                                        <label>
                                            <input type="checkbox"> 显示电子围栏
                                        </label>
                                    </div>
                                    <div class="checkbox">
                    -->
                    <label>
                        <input type="checkbox" id="show-bike-number"> <span class="hidden-phone hidden-xs hidden-sm hidden-md">显示单车</span>编号
                    </label>
                </div>
                |
                <div class="dropdown show-bike-select" style="margin-right: 0;">
                    <button class="btn btn-default btn-xs btn-no-border dropdown-toggle" type="button" id="dropdownOfShowBikes" data-toggle="dropdown" style="font-size: 14px; vertical-align: baseline;">
                        全部单车
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownOfShowBikes">
                        <li class="active" data-bike_type=""><a href="#">全部单车</a></li>
                        <li role="separator" class="divider"></li>
                        <li data-bike_type="low_battery"><a href="#">电量低</a></li>
                        <li data-bike_type="illegal_parking"><a href="#">违停</a></li>
                        <li data-bike_type="fault"><a href="#">故障</a></li>
                        <li role="separator" class="divider"></li>
                        <li data-bike_type="offline"><a href="#">失联</a></li>
                    </ul>
                </div>
                <button type="button" class="btn btn-default btn-xs btn-no-border hidden-phone" style="font-size: 14px; vertical-align: baseline;">
                    <i class="fa fa-file-excel-o"></i> <span class="hidden-xs hidden-sm hidden-md">导出Excel</span>
                </button>
            </div>
            <div id="statistics">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <h1 class="dashboard-title"><i class="fa fa-cny fa-fw"></i> 财务中心<span> Financial Center</span></h1>
                    <ul>
                        <li>
                            <div class="statistics-block-title">现金</div>
                            <div class="statistics-block tow-lines-block">今日收入：￥<?php echo $recharge_sum['list'][0]['total']?$recharge_sum['list'][0]['total']:'0.00' ?><br/>总金额：￥<?php echo $recharge_sum['total']?$recharge_sum['total']:'0.00' ?></div>
                        </li>
                        <li>
                            <div class="statistics-block-title">押金</div>
                            <div class="statistics-block three-lines-block">今日内交：￥<?php echo $deposit_sum['recharge'][0]['total']?$deposit_sum['recharge'][0]['total']:'0.00' ?><br/>总金额：￥<?php echo $deposit_sum['total']?$deposit_sum['total']:'0.00' ?><br/>今日退押金：￥<?php echo $deposit_sum['cash'][0]['total']?$deposit_sum['cash'][0]['total']:'0.00' ?></div>
                        </li>
                        <li>
                            <div class="statistics-block-title">用车券</div>
                            <div class="statistics-block tow-lines-block">今日已使用：<?php echo $coupon_sum['list'][0]['total']?$coupon_sum['list'][0]['total']:'0' ?>张<br/>库存：<?php echo $coupon_sum['total']?$coupon_sum['total']:'0' ?>张</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="bikes" class="hidden-mobile hidden-xs no-padding">
            <form>
            <div class="dropdown show-bike-type-select">
                <button class="btn btn-default btn-xs btn-no-border dropdown-toggle from-control" type="button" data-toggle="dropdown">
                    违停/故障
                    <span class="caret pull-right"></span>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" checked> 违停
                            </label>
                        </div>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" checked> 故障
                            </label>
                        </div>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox"> 电量低
                            </label>
                        </div>
                        </a>
                    </li>
                </ul>
            </div>
            </form>
            <!-- search form -->
            <form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="搜索单车...">
                    <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                    </button>
                  </span>
                </div>
            </form>
            <!-- /.search form -->
            <ul class="bike-list">
                <li>650312</li>
                <li>650311</li>
                <li>710412</li>
                <li>789121</li>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery.getScript('<?php echo HTTP_IMAGE;?>js/coordinate.js');
    jQuery.getScript('<?php echo HTTP_IMAGE;?>js/plugins/bootstrap-treeview.min.js');
    jQuery.getScript('<?php echo HTTP_IMAGE;?>js/plugins/jquery.sparkline.min.js');
    jQuery.getScript('<?php echo HTTP_IMAGE;?>js/admin/sparkline.js');
    jQuery.getScript('<?php echo HTTP_IMAGE;?>js/admin/magnific-popup.js');
    jQuery.getScript('<?php echo HTTP_IMAGE;?>js/admin/index.js');
    window.imageUrlBase = '<?php echo HTTP_IMAGE;?>';
</script>
<script type="text/javascript"  src="http://webapi.amap.com/maps?v=1.3&key=38c88d25e4aa2652bc7806db2d1f6a0d&plugin=AMap.Geocoder&callback=initMap"></script>

<?php echo $footer;?>