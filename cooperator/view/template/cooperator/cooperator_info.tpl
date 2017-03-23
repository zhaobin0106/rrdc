<?php echo $header; ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>合伙人详情<small>合伙人详情</small></h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i>首页</li>
        <li>合伙人详情</li>
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
                        <label for="" class="col-sm-4 control-label">合伙人名称</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['cooperator_name']; ?></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">状态</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['state']; ?></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">最后登录时间</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['login_time']; ?></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">最后登录IP</label>
                        <div class="col-sm-8">
                            <span><?php echo $data['login_ip']; ?></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="col-sm-4 control-label">添加时间</label>
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
