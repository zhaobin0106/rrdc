/**
 * Created by Administrator on 2017/1/20.
 */
jQuery(function ($) {
    /**
     * 全局变量：地图
     * @type {null/Object}
     */
    var map = null;

    /**
     * 全局变量：地图是否已经被初始化了
     * @type {boolean}
     */
    var marker_init = false;


    /**
     * 全局变量：记录地图上的所有marker点
     * @type {Array}
     * @private
     */
    var _markers = [];

    /**
     * 全局变量：当前打开的infoWindow是属于哪个单车的（记录单车的id，如果没有打开infoWindow则为false）
     * @type {boolean}
     */
    var infoWindowOpened = false;

    /**
     * 全局变量：infoWindow
     * @type {null/Object}
     */
    var infoWindow = null;

    /**
     * 全局变量：infoWindow的单车信息JQuery对象
     * @type {null}
     */
    var $bikeInfo = null;

    /**
     * 全局变量：当前是否显示单车编号，默认false
     * @type {boolean}
     */
    var showBikeNumber = false;

    /**
     * 全局变量：当前显示单车的状态：''代表所有单车，其他值还有'low_battery'、'illegal_parking'、'fault'、'offline'
     * @type {string}
     */
    var showBikeStatusType = '';

    /**
     * 全局变量：下次加载完Marker点之后（添加Marker点之前）是否要清除Marker点
     */
    var clearMarkerOnNextLoad = false;

    /////////////////////////////////////////---地图---////////////////////////////////////////

    /**
     * 初始化地图
     */
    window.initMap = function(){
        if(typeof AMap != 'undefined') {
            map = map || new AMap.Map("map", {
                    resizeEnable:true,
                    zoom:14
                });

            // 地图事件处理
            map.on('resize', loadMarker);
            map.on('zoomchange', loadMarker);
            map.on('moveend', loadMarker);

            // 创建InfoWindow，加载单车Marker点
            createInfoWindow();
            loadMarker();
            addTool();

            // 初始化测距工具
            initRuler();
        }
    };

    /**
     * 加载单车Marker（Ajax请求后台admin/index/apiGetMarker）
     */
    function loadMarker() {
        var bounds = map.getBounds(),
            sw = bounds.getSouthWest(),
            ne = bounds.getNorthEast(),
            swGps = gcj02towgs84(Number(sw.getLng()), Number(sw.getLat())),
            neGps = gcj02towgs84(Number(ne.getLng()), Number(ne.getLat())),
            data = {min_lng: swGps[0], min_lat: swGps[1], max_lng: neGps[0], max_lat: neGps[1]};
        if(showBikeStatusType!='') data.status = showBikeStatusType;
        if(!marker_init) data.marker_init = 1;
        $('.btn-refresh-marker > i').addClass('fa-spin');
        $('.btn-refresh-marker').prop('disabled', true);
        $.ajax('index.php?route=admin/index/apiGetMarker', {
            data: data,
            dataType: 'json',
            method: 'post',
            global: false,
            success: mrkerLoaded
        });
    }

    /**
     * 加载单车Marker点（Ajax请求成功的操作）
     * @param data
     */
    function mrkerLoaded(data) {
        console.log(data);

        if(clearMarkerOnNextLoad) {
            map.clearMap();
            _markers = new Array();
            clearMarkerOnNextLoad = false;
        }

        $.each(data.data,function(i, bike) {
            var marker,
                battery = Math.abs(parseInt(bike.battery)),
                pos = wgs84togcj02(Number(bike.lng), Number(bike.lat)); //坐标转换
            // 根据电量决定图标
            if(bike.online=="0") {
                bike.iconContent = '<img src="../static/images/map_icon/j4.png">';
                bike.textContent = '<div class="bike-number-text-marker offline">' + bike.bicycle_sn + '</div>';
            }
            else if(battery>50) {
                bike.iconContent = '<img src="../static/images/map_icon/j1.png">';
                bike.textContent = '<div class="bike-number-text-marker battery-ok">' + bike.bicycle_sn + '</div>';
            }
            else if(battery>25) {
                bike.iconContent = '<img src="../static/images/map_icon/j3.png">';
                bike.textContent = '<div class="bike-number-text-marker battery-warning">' + bike.bicycle_sn + '</div>';
            }
            else {
                bike.iconContent = '<img src="../static/images/map_icon/j2.png">';
                bike.textContent = '<div class="bike-number-text-marker battery-low">' + bike.bicycle_sn + '</div>';
            }

            if(!_markers[bike.bicycle_id]){ //新的Marker点，添加到地图
                marker = new AMap.Marker({
                    content:showBikeNumber ? bike.textContent : bike.iconContent,
                    position:pos,
                    offset:showBikeNumber ? new AMap.Pixel(-30,-25) : new AMap.Pixel(-17,-32),//X轴Y轴
                    map: map
                });
                marker.on('click', onMarkerClick);
                _markers[bike.bicycle_id] = marker;
            }
            else { //已有的Marker点，更新图标和位置
                marker = _markers[bike.bicycle_id];
                if(marker.bike.showingNumber!=showBikeNumber) {
                    marker.setContent(showBikeNumber ? bike.textContent : bike.iconContent);
                    marker.setOffset(showBikeNumber ? new AMap.Pixel(-30,-25) : new AMap.Pixel(-17,-32));
                }
                var oldPos = marker.getPosition();
                if((Math.abs(oldPos.getLng() - pos[0]) > 0.000001) || (Math.abs(oldPos.getLat() - pos[1]) > 0.000001) ) {
                    marker.setPosition(pos);
                }
            }
            bike.showingNumber = showBikeNumber;
            marker.bike = bike; //把单车数据更新给Marker点（记录下来）

            if(infoWindowOpened == bike.bicycle_id) { //如果infoWindow已经打开，立刻更新上面的内容
                updateInfoWindow(bike);
            }
        });

        if(!marker_init) {
            marker_init = true;
            map.setFitView(); //如果是第一次加载到marker点，setFitView到显示所有点。//TODO 记录最后的中心和zoom
        }

        $('.btn-refresh-marker').prop('disabled', false);
        $('.btn-refresh-marker > i').removeClass('fa-spin');
    }

    /**
     * 加载地图上的控件
     */
    function addTool() {
        map.plugin(["AMap.ToolBar"], function() {
            map.addControl(new AMap.ToolBar());
        });
    }


    /**
     * 创建InfoWindow
     */
    function createInfoWindow() {
        // infoWindow的内容模板
        $bikeInfo = $(
            '<div>' + // 加一层div方便下面获取dom（$bikeInfo[0]）
                '<div class="bike-info">' +
                    '<div class="bike-info-title">自行车编号：<span class="bike-sn">123456</span></div>' +
                    '<ul class="bike-info-tabs">' +
                        '<li class="active">概况</li>' +
                        '<li>故障</li>' +
                        '<li>违停</li>' +
                        '<li>停车</li>' +
                        '<li>反馈</li>' +
                        '<li>使用</li>' +
                        '<li>指令</li>' +
                    '</ul>' +
                    '<div class="bike-info-body">' +
                        '<div class="active">' + // 概况 start
                            '<div class="icon-info-wrapper">' +
                                '<div class="bike-battery battery-1" title="电池电压：3.83V\n充电电压：0.06V"></div>' +
                                '<div class="bike-power"></div>' +
                            '</div>' +
                            '<div class="icon-info-wrapper">' +
                                '<div class="bike-status bike-status-locked"></div>' +
                                '<div class="bike-status-text">在线</div>' +
                            '</div>' +
                            '<div class="icon-info-wrapper">' +
                                '<div class="bike-alarm"></div>' +
                                '<div class="bike-alarm-text">无警报</div>' +
                            '</div>' +
                            '<div class="icon-info-wrapper">' +
                                '<div class="bike-gprs signal-3" title="24"></div>' +
                                '<div>GPRS</div>' +
                            '</div>' +
                            '<div class="icon-info-wrapper">' +
                                '<div class="bike-gps signal-3" title="6"></div>' +
                                '<div>GPS</div>' +
                            '</div>' +
                            '<div class="horizontal-info">' +
                                '<div>故障：<i class="fa fa-circle"></i></div>' +
                                '<div>违停：<i class="fa fa-circle"></i></div>' +
                                '<div>反馈：<i class="fa fa-circle"></i></div>' +
                            '</div>' +
                            '<hr>' +
                            '<div class="bike-location font14"><i class="fa fa-map-marker fa-fw"></i> <span>广东省东莞市南城区新基路新基地创意产业园H座</span></div>' +
                            '<div class="bike-last-update font14"><i class="fa fa-clock-o fa-fw"></i> 最后更新：<span class="last-update-time">2017-03-07 22:22:22</span>(<span class="last-update-type">GPS</span>)<!-- <button class="btn btn-xs btn-default"><i class="fa fa-map"></i> 轨迹</button> --></div>' +
                            '<hr>' +
                            '<div class="font14"><span class="bike-cooperator"><i class="fa fa-user fa-fw"></i> 西通电子</span>' +
                            '<span class="bike-region"><i class="fa fa-map-pin fa-fw"></i> 珠海市<span></div>' +
                            '<hr>' +
                            '<div class="bike-lock-sn font14"><i class="fa fa-lock fa-fw"></i> 锁编号：<span>063012345678</span> <i class="fa fa-info-circle bike-lock-info"></i></div>' +
                            '<div class="bike-lock-type font14"><i class="fa fa-compass fa-fw"></i> <span>锁类型：GRPS</span></div>' + // fa-bluetooth
                            '<hr>' +
                            '<div class="bike-used-times-total font14"><i class="fa fa-area-chart fa-fw"></i> <span>使用次数：共23次，本月13次，今天3次</span></div>' +
                        '</div>' + // 概况 end
                        '<div><ul class="bike-info-list"></ul></div>' + // 故障
                        '<div><ul class="bike-info-list"></ul></div>' + // 违停
                        '<div><ul class="bike-info-list"></ul></div>' + // 停车
                        '<div><ul class="bike-info-list"></ul></div>' + // 反馈
                        '<div><ul class="bike-info-list"></ul></div>' + // 使用
                        '<div class="bike-instruction">' +  // 指令 start
                            '<button class="btn btn-default"><i class="fa fa-lock fa-fw"></i> 关锁</button>' +
                            '<button class="btn btn-default"><i class="fa fa-unlock-alt fa-fw"></i> 开锁</button><br/>' +
                            '<button class="btn btn-default"><i class="fa fa-bell fa-fw"></i> 开蜂鸣器</button>' +
                            '<button class="btn btn-default"><i class="fa fa-bell-slash fa-fw"></i> 关蜂鸣器</button><br/>' +
                            '<div class="input-group">' +
                                '<span class="input-group-addon">关锁时：每隔</span>' +
                                '<input type="number" class="form-control" value="1800">' +
                                '<span class="input-group-addon">秒一次定位</span>' +
                                '<span class="input-group-btn">' +
                                    '<button class="btn btn-default" type="button">设置</button>' +
                                '</span>' +
                            '</div>' +
                            '<div class="input-group">' +
                                '<span class="input-group-addon">开锁时：每隔</span>' +
                                '<input type="number" class="form-control" value="600">' +
                                '<span class="input-group-addon">秒一次定位</span>' +
                                '<span class="input-group-btn">' +
                                    '<button class="btn btn-default" type="button">设置</button>' +
                                '</span>' +
                            '</div>' +
                        '</div>' + // 指令 end
                        '<div class="loading-mask"><div><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i> <span class="sr-only">Loading...</span></div></div>' +
                        '<div class="bike-info-qrcode"><img src="http://120.76.72.228/orange/static/images/qrcode/1488629999.png" STYLE=""/></div>' +
                    '</div>' +
                '</div>' +
                '<div class="bike-info-sharp"></div>' +
                '<a class="bike-info-close" href="javascript: void(0)"></a>' +
                '<div class="bike-info-qrcode-trigger"><i class="fa fa-qrcode"></i></div>' +
                '<div class="bike-info-refresh"><i class="fa fa-refresh"></i></div>' +
            '</div>'
        );

        infoWindow = new AMap.InfoWindow({
            isCustom: true,
            content: $bikeInfo[0],
            offset: showBikeNumber ? new AMap.Pixel(0, -25) : new AMap.Pixel(0, -35)
        });

        infoWindow.on('open', function(){
            setTimeout(function(){
                if($bikeInfo.parent().length){
                    $bikeInfo.parent().parent().css('bottom', showBikeNumber ? '25px' : '35px');
                }
            });
        });

        ///////////////// infoWindow上的事件处理
        $bikeInfo.on('click', '.bike-info-close', function(){ // 右上角的关闭按钮
            setActive($bikeInfo.find('.bike-info-tabs > li:first-child'));  //重置第一个标签为active
            setActive($bikeInfo.find('.bike-info-body > div:first-child'));
            map.setStatus({scrollWheel:true});
            map.clearInfoWindow();
            infoWindowOpened = false;
        }).on('click', '.bike-info-refresh', function() { // 右上角的刷新按钮
            $(this).find('i').addClass('fa-spin');
            refreshTab();
        }).on('mouseenter', '.bike-info-qrcode-trigger', function() { // 右上角二维码按钮图标（鼠标进入）
            setActive($bikeInfo.find('.bike-info-body > div:last-child'));
        }).on('mouseleave', '.bike-info-qrcode-trigger', function() { // 右上角二维码按钮图标（鼠标离开
            setActive($bikeInfo.find('.bike-info-body > div:eq(' + $bikeInfo.find('.bike-info-tabs > li.active').index() + ')'));
        }).on('click', '.bike-info-tabs > li', function() { //切换标签
            var index = $(this).index(),
                $tabDiv = $(this).parent().next().children().eq(index);
            setActive($(this));
            setActive($tabDiv);
            if(index>0 && index<6) {
                map.setStatus({scrollWheel:false});
            }
            else {
                map.setStatus({scrollWheel:true});
            }
            if($tabDiv.data('data-loaded')) return;
            refreshTab();
        }).on('click', '.bike-info-list > li.has-more > button', function() { // 故障、违停、停车、反馈、使用等内部的“加载更多”按钮
            var index = $(this).parent().parent().parent().index(),
                page = $(this).parent().data('next');
            loadTabData(index, page);
        });
        $bikeInfo.magnificPopup({
            delegate: '.bike-info-list-img > img',
            type: 'image',
            mainClass: 'mfp-with-zoom',
            zoom: {
                enabled: true,
                duration: 300,
                easing: 'ease-in-out'
            }
        });
    }


    /**
     * 点击Marker点（先更新InfoWindow的内容，然后显示InfoWindow）
     * @param e
     */
    function onMarkerClick(e) {
        var marker = e.target,
            bike = marker.bike;
        updateInfoWindow(bike);

        infoWindow.open(map, marker.getPosition());
        infoWindowOpened = bike.bicycle_id;
    }

    /**
     * 根据单车的信息更新InfoWindow的内容
     * @param bike
     */
    function updateInfoWindow(bike) {
        // 编号
        $bikeInfo.find('.bike-sn').html(bike.bicycle_sn);
        // 否在线
        bike.online=="0" ? $bikeInfo.find('.bike-info').removeClass("online") : $bikeInfo.find('.bike-info').addClass("online");
        // 电量
        var battery = Math.abs(parseInt(bike.battery));
        $bikeInfo.find('.bike-power').html(battery + '%');
        if(parseInt(bike.battery)>=0) {
            $bikeInfo.find('.bike-battery').removeClass("battery-1 battery-2 battery-3").addClass("battery-0");
        }
        else if(battery>=50) {
            $bikeInfo.find('.bike-battery').removeClass("battery-0 battery-1 battery-2").addClass("battery-3");
        }
        else if(battery>25) {
            $bikeInfo.find('.bike-battery').removeClass("battery-0 battery-1 battery-3").addClass("battery-2");
        }
        else {
            $bikeInfo.find('.bike-battery').removeClass("battery-0 battery-2 battery-3").addClass("battery-1");
        }
        $bikeInfo.find('.bike-battery').attr('title', "电池电压：" + bike.battery_voltage + "V\n充电电压：" + bike.charging_voltage + "V");
        // 开锁关锁状态
        if(bike.lock_status=="0") {
            $bikeInfo.find('.bike-status').removeClass("bike-status-unlocked bike-status-error").addClass("bike-status-locked");
            $bikeInfo.find('.bike-status-text').html('关');
        }
        else if(bike.lock_status=="1") {
            $bikeInfo.find('.bike-status').removeClass("bike-status-locked bike-status-error").addClass("bike-status-unlocked");
            $bikeInfo.find('.bike-status-text').html('开');
        }
        else if(bike.lock_status=="2") {
            $bikeInfo.find('.bike-status').removeClass("bike-status-unlocked bike-status-locked").addClass("bike-status-error");
            $bikeInfo.find('.bike-status-text').html('异常');
        }
        // 状态与报警
        // 运动或静止
        if(bike.moving=="1" && bike.lock_status=="1") {
            $bikeInfo.find('.bike-alarm').removeClass("static-status").addClass("moving-status");
            $bikeInfo.find('.bike-alarm-text').addClass("moving-status").html("运动中");
        }
        else {
            $bikeInfo.find('.bike-alarm').removeClass("moving-status").addClass("static-status");
            $bikeInfo.find('.bike-alarm-text').removeClass("moving-status").html("静止");
        }
        // 低电量报警
        if(bike.low_battary_alarm=="1") {
            $bikeInfo.find('.bike-alarm').removeClass("moving-status").addClass("low-battery-alarm");
            $bikeInfo.find('.bike-alarm-text').html("低电量");
        }
        else {
            $bikeInfo.find('.bike-alarm').removeClass("low-battery-alarm");
        }
        // 非法移动报警
        if(bike.illegal_moving_alarm=="1") {
            $bikeInfo.find('.bike-alarm').removeClass("moving-status").addClass("illegal-moving-alarm");
            $bikeInfo.find('.bike-alarm-text').html("非法移动");
        }
        else {
            $bikeInfo.find('.bike-alarm').removeClass("illegal-moving-alarm");
        }

        // GPRS信号
        var gprs = Number(bike.gprs);
        $bikeInfo.find('.bike-gprs').attr('title', gprs);
        if(gprs<10) {
            $bikeInfo.find('.bike-gprs').removeClass("signal-1 signal-2 signal-3 signal-4 signal-5").addClass("signal-0");
        } else if(gprs<15) {
            $bikeInfo.find('.bike-gprs').removeClass("signal-0 signal-2 signal-3 signal-4 signal-5").addClass("signal-1");
        } else if(gprs<20) {
            $bikeInfo.find('.bike-gprs').removeClass("signal-0 signal-1 signal-3 signal-4 signal-5").addClass("signal-2");
        } else if(gprs<25) {
            $bikeInfo.find('.bike-gprs').removeClass("signal-0 signal-1 signal-2 signal-4 signal-5").addClass("signal-3");
        } else if(gprs<30) {
            $bikeInfo.find('.bike-gprs').removeClass("signal-0 signal-1 signal-2 signal-3 signal-5").addClass("signal-4");
        } else {
            $bikeInfo.find('.bike-gprs').removeClass("signal-0 signal-1 signal-2 signal-3 signal-4").addClass("signal-5");
        }
        // GPS信号
        var gps = Number(bike.gps);
        $bikeInfo.find('.bike-gps').attr('title', gps);
        if(gps<1) {
            $bikeInfo.find('.bike-gps').removeClass("signal-1 signal-2 signal-3 signal-4 signal-5").addClass("signal-0");
        } else if(gps<3) {
            $bikeInfo.find('.bike-gps').removeClass("signal-0 signal-2 signal-3 signal-4 signal-5").addClass("signal-1");
        } else if(gps<5) {
            $bikeInfo.find('.bike-gps').removeClass("signal-0 signal-1 signal-3 signal-4 signal-5").addClass("signal-2");
        } else if(gps<7) {
            $bikeInfo.find('.bike-gps').removeClass("signal-0 signal-1 signal-2 signal-4 signal-5").addClass("signal-3");
        } else if(gps<9) {
            $bikeInfo.find('.bike-gps').removeClass("signal-0 signal-1 signal-2 signal-3 signal-5").addClass("signal-4");
        } else {
            $bikeInfo.find('.bike-gps').removeClass("signal-0 signal-1 signal-2 signal-3 signal-4").addClass("signal-5");
        }

        // 最后更新时间
        $bikeInfo.find('.bike-last-update > span.last-update-time').html(bike.last_update);
        if(bike.gps_positioning=='1') {
            $bikeInfo.find('.bike-last-update > span.last-update-type').html('GPS');
        } else {
            $bikeInfo.find('.bike-last-update > span.last-update-type').html('LBS');
        }
        // 锁编号
        $bikeInfo.find('.bike-lock-sn > span').html(bike.lock_sn);
        // 地区
        $bikeInfo.find('.bike-region').html('<i class="fa fa-map-pin fa-fw"></i> ' + bike.region_name);

        // 二维码
        $bikeInfo.find('.bike-info-qrcode > img').attr("src", window.imageUrlBase + "images/qrcode/" + bike.bicycle_sn + '.png');
    }

    /**
     * 刷新当前标签
     */
    function refreshTab() {
        var $tabDiv = $bikeInfo.find('.bike-info-body > div.active'),
            index = $tabDiv.index();

        if(index<0 || index>6) return; //只有7个标签（0-6）

        if(index>0 && index<6) $tabDiv.find('ul').empty(); //0和6是不清空内容的，只更新内容

        switch (index) {
            case 0:
            case 6:
                refreshGeneral();
                break;
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
                $tabDiv.find('ul').empty();
                loadTabData(index, 1);
                break;
        }
    }

    /**
     * 加载成功当前标签内容（除了概况和指令之外）
     * @param index
     * @param page
     */
    function loadTabData(index, page) {
        $bikeInfo.find('.bike-info-body').addClass('loading');
        $bikeInfo.find('.bike-info-body > div.active > ul.bike-info-list > li.has-more').remove();
        switch (index) {
            case 1: // 故障
                loadFault(page);
                break;
            case 2: // 违停
                loadIlleagleParking(page);
                break;
            case 3: // 停车
                loadNormalParking(page);
                break;
            case 4: // 反馈
                loadFeekback(page);
                break;
            case 5: // 使用
                loadUsedHistory(page);
                break;
        }
    }

    /**
     * TODO 刷新当前标签的概况
     */
    function refreshGeneral() { //刷新概况，包括指令标签里面的定位上传间隔
        $bikeInfo.find('.bike-info-body').addClass('loading');

        tabDataLoaded($bikeInfo.find('.bike-info-body > div:eq(0)'));
    }

    /**
     * 加载故障列表
     * @param page
     */
    function loadFault(page) {
        var $tabDiv = $bikeInfo.find('.bike-info-body > div:eq(1)');
        $.ajax('index.php?route=admin/index/apiGetFaults', {
            dataType: 'html',
            data: {page: page},
            method: 'POST',
            global: false,
            success: function (html) {
                $tabDiv.find('ul').append(html);
                tabDataLoaded($tabDiv);
            }
        });
    }

    /**
     * 加载违停列表
     * @param page
     */
    function loadIlleagleParking(page) {
        var $tabDiv = $bikeInfo.find('.bike-info-body > div:eq(2)');
        $.ajax('index.php?route=admin/index/apiGetIllegalParking', {
            dataType: 'html',
            data: {page: page},
            method: 'POST',
            global: false,
            success: function (html) {
                $tabDiv.find('ul').append(html);
                tabDataLoaded($tabDiv);
            }
        });
    }

    /**
     * 加载停车列表
     * @param page
     */
    function loadNormalParking(page) {
        var $tabDiv = $bikeInfo.find('.bike-info-body > div:eq(3)');
        $.ajax('index.php?route=admin/index/apiGetNormalParking', {
            dataType: 'html',
            data: {page: page},
            method: 'POST',
            global: false,
            success: function (html) {
                $tabDiv.find('ul').append(html);
                tabDataLoaded($tabDiv);
            }
        });
    }

    /**
     * 加载反馈列表
     * @param page
     */
    function loadFeekback(page) {
        var $tabDiv = $bikeInfo.find('.bike-info-body > div:eq(4)');
        $.ajax('index.php?route=admin/index/apiGetFeekbacks', {
            dataType: 'html',
            data: {page: page},
            method: 'POST',
            global: false,
            success: function (html) {
                $tabDiv.find('ul').append(html);
                tabDataLoaded($tabDiv);
            }
        });
    }

    /**
     * 加载使用记录列表
     * @param page
     */
    function loadUsedHistory(page) {
        var $tabDiv = $bikeInfo.find('.bike-info-body > div:eq(5)');
        $.ajax('index.php?route=admin/index/apiGetUsedHistory', {
            dataType: 'html',
            data: {page: page},
            method: 'POST',
            global: false,
            success: function (html) {
                $tabDiv.find('ul').append(html);
                tabDataLoaded($tabDiv);
            }
        });
    }

    /**
     * 标签内容加载完毕后的处理
     * @param $tabDiv
     */
    function tabDataLoaded($tabDiv) {
        $bikeInfo.find('.bike-info-body').removeClass('loading');
        $bikeInfo.find('.bike-info-refresh > i').removeClass('fa-spin');
        $tabDiv.data('data-loaded', true);
    }


    /////////////////////////////////////////---工具栏---////////////////////////////////////////

    /**
     * 合伙人和景区列表（树）
     */
    var data = [{
        text: '西通电子',
        place_id: 0,
        state: {
            selected: true
        },
        nodes:[{
            text: '珠海市',
            place_id: 1,
            state: {
                expanded: true
            }
        }]
    }];
    var treeview = $('#treeview');
    treeview.treeview({data: data});

    /**
     * 合伙人和景区列表（树）的事件处理
     */
    treeview.on('click', 'ul li span.expand-icon', function(e){ // 点击树上的展开或者收缩按钮
        e.preventDefault();
        e.stopPropagation();

        var nodeid = $(this).parent().data('nodeid');
        treeview.treeview('toggleNodeExpanded', nodeid);
    }).on('click', 'ul li', function(){ // 点击树的某一项
        var nodeid = $(this).data('nodeid'), t = '';
        console.log(treeview.treeview('getNode', nodeid).place_id);
        if(nodeid!=0) {
            do{
                var parent = treeview.treeview('getParent', nodeid);
                if(parent && parent.nodeId!=0) {
                    t = parent.text + ' - ' + t;
                    nodeid = parent.nodeId;
                }
            }while(parent && parent.nodeId!=0);
        }
        $(this).parentsUntil('.dropdown').last().prev().html(t + $(this).text() + ' <span class="caret"></span>');
    });

    /**
     * 工具栏上的刷新按钮：重新刷新Marker点
     */
    $('.btn-refresh-marker').on('click', loadMarker);

    /**
     * TODO 工具栏上的“标注”按钮
     */
    $('.btn-map-label').on('click', function () {

    });

    /**
     * 初始化测距工具
     */
    var ruler; //测距工具
    function initRuler() {
        map.plugin(["AMap.RangingTool"], function() {
            ruler = new AMap.RangingTool(map);
            AMap.event.addListener(ruler, "end", function(e) {
                ruler.turnOff();
                map.setDefaultCursor(__defaultCursor);
            });
        });
    }
    /**
     * 工具栏上的工具：测距和分享
     */
    $('.tools-select').on('click', '> li.tool-ruler', function () { // 测距
        __defaultCursor = map.getDefaultCursor();
        map.setDefaultCursor('crosshair');
        ruler.turnOn();
    }).on('click', '> li.tool-share', function () { // TODO 分享

    });
    var __defaultCursor;

    /**
     * 全屏地图
     */
    $('.btn-map-maximize').on('click', function() {
        if(__map_maximized) {
            $('body').removeClass('map-maximized');
            $(this).removeClass('active');
        }
        else {
            $('body').addClass('map-maximized');
            $(this).addClass('active');
        }
        __map_maximized = !__map_maximized;
        $(this).blur();
    });
    var __map_maximized = false;

    /**
     * 显示单车编号
     */
    $('input#show-bike-number').on('change', function(){
        showBikeNumber = $(this).prop('checked');
        $.each(_markers, function (index, marker) {
            if(typeof marker != 'undefined') {
                marker.setContent(showBikeNumber ? marker.bike.textContent : marker.bike.iconContent);
                marker.setOffset(showBikeNumber ? new AMap.Pixel(-30,-25) : new AMap.Pixel(-17,-32));
            }
        });
        if($bikeInfo.parent().length){
            $bikeInfo.parent().parent().css('bottom', showBikeNumber ? '25px' : '35px');
        }
    });


    /**
     * TODO 工具栏上显示单车的选择菜单
     */
    $('.show-bike-select li').on('click', function() {
        if($(this).hasClass('active')) return;
        $(this).parent().prev().html($(this).find('a').text() + ' <span class="caret"></span>');
        setActive($(this));
        showBikeStatusType = $(this).data('bike_type');
        clearMarkerOnNextLoad = true;
        loadMarker();
    });

    /////////////////////////////////////////---单车列表---////////////////////////////////////////

    $('.show-bike-type-select li').on('click', function() {
        var checkbox = $(this).find('input[type="checkbox"]');
        checkbox.prop('checked', !checkbox.prop('checked'));
        updateBikeListType();
    });

    $('.show-bike-type-select li input[type="checkbox"]').on('click', function() {
        $(this).prop('checked', !$(this).prop('checked'));
        console.log(this, $(this),$(this).prop('checked'));

        updateBikeListType();
    });

    function updateBikeListType() {
        var types = [];
        $('.show-bike-type-select input[type="checkbox"]:checked').each(function() {
            var t = $.trim($(this).parent().text());
            if(t.length) types.push(t);
        });
        var html = (types.length ? types.join('/') : '(全部)') + ' <span class="caret pull-right"></span>';
        $('.show-bike-type-select button').html(html);
    }


    /////////////////////////////////////////---公共函数---////////////////////////////////////////
    /**
     * 设置某个元素是active，其siblings都去掉active
     * @param $dom
     */
    function setActive($dom) {
        $dom.addClass('active').siblings().removeClass('active');
    }

});