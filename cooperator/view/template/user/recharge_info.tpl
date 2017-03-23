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
                            <label for="" class="col-sm-4 control-label">订单号</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['pdr_sn']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">用户名</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['mobile']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">充值金额</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['pdr_amount']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">充值类型</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['pdr_type']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">支付状态</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['pdr_payment_state']; ?></span>
                            </div>
                        </div>
                        <?php if ($data['pdr_payment_state'] == 1) { ?>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">支付方式</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['pdr_payment_name']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">交易号</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['pdr_trade_sn']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">支付时间</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['pdr_payment_time']; ?></span>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">管理员名称</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['pdr_admin']; ?></span>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="" class="col-sm-4 control-label">下单时间</label>
                            <div class="col-sm-8">
                                <span><?php echo $data['pdr_add_time']; ?></span>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php echo $footer;?>
