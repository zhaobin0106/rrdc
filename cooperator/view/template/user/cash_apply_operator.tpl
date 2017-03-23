<?php echo $header; ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1><?php echo $title; ?></h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i>首页</li>
        <li><?php echo $title; ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border"></div>
                <div class="box-body">
                    <?php if (isset($error['warning'])) { ?>
                    <div class="alert alert-danger" style="opacity: 0.8;"><i class="fa fa-exclamation-circle"></i>
                        <span><?php echo $error['warning'];?></span>
                        <button type="button" class="close" data-dismiss="alert">x</button>
                    </div>
                    <?php } ?>
                    <form id="myForm" class="form-horizontal" method="post" action="<?php echo $action; ?>">
                        <input type="hidden" name="pdc_id" value="<?php echo $pdc_id; ?>" >
                        <input type="hidden" name="type" id="type">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">提现编号：</label>
                                <div class="col-sm-8">
                                    <span><?php echo $data['pdc_sn'];?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">用户：</label>
                                <div class="col-sm-8">
                                    <span><?php echo $data['pdc_user_name'];?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">提现金额：</label>
                                <div class="col-sm-8">
                                    <span><?php echo $data['pdc_amount'];?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">申请时间：</label>
                                <div class="col-sm-8">
                                    <span><?php echo $data['pdc_add_time'];?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">支付时间：</label>
                                <div class="col-sm-8">
                                    <span><?php echo $data['pdc_payment_time'];?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">提现状态：</label>
                                <div class="col-sm-8">
                                    <span><?php echo $data['pdc_payment_state'];?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">充值编号：</label>
                                <div class="col-sm-8">
                                    <span><?php echo $data['pdr_sn'];?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="button" data-type="agree" class="btn btn-success opr pull-right" style="margin-right: 16px;" value="同意提现">
                                    <input type="button" data-type="disagree" class="btn btn-danger opr pull-right" style="margin-right: 5px;" value="取消提现">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(function () {
        $('.opr').click(function () {
            var $type = $(this).data('type');
            var msg = $type == 'disagree' ? '您确定要取消提现么？' : "您确定要同意提现吗？";
            if (confirm(msg)) {
                $('#type').val($type);
                $('#myForm').submit();
            }
        });
    });
</script>
<?php echo $footer; ?>