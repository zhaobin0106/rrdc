<?php echo $header; ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><small>消费记录详情</small></h1>
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

                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">订单sn</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['order_sn']; ?></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">用户名</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['user_name']; ?></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">锁sn</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['lock_sn']; ?></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">单车sn</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['bicycle_sn']; ?></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">景区</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['region_name']; ?></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">开始时间</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['start_time']; ?></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">出发经度</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['start_lng']; ?></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">出发纬度</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['start_lat']; ?></span>
                        </div>
                    </div>
                    <?php if ($data['order_status'] == 2) { ?>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">结束时间</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['end_time']; ?></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">订单金额（元）</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['order_amount']; ?></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">结束经度</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['end_lng']; ?></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">结束纬度</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['end_lat']; ?></span>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">订单状态</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['order_state']; ?></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">下单时间</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['add_time']; ?></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<?php echo $footer;?>
