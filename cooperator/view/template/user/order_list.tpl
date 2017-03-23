<?php echo $header; ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>充值记录列表<small>充值记录列表</small></h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i>首页</li>
        <li>充值记录列表</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <form action="" method="post" id="export-form" style="display: none">
                        <input type="hidden" name="page_export" id="page_export">
                        <input type="hidden" name="current_page" id="current_page" value="1">
                    </form>
                </div>
                <form class="search_form" action="<?php echo $action; ?>" method="get">
                    <div class="box-body">
                        <?php if (isset($error['warning'])) { ?>
                        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>&nbsp;<?php echo $error['warning']; ?>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <?php } ?>
                        <?php if (isset($success)) { ?>
                        <div class="alert bg-light-blue"><i class="fa fa-check-circle"></i>&nbsp;<?php echo $success; ?>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <?php } ?>
                        <table class="table table-bordered table-hover dataTable" role="grid">
                            <thead>
                            <tr>
                                <?php foreach ($data_columns as $column) { ?>
                                <th><?php echo $column['text']; ?></th>
                                <?php } ?>
                                <th style="min-width:130px;">操作</th>
                            </tr>
                            <tr class="searchbar">
                                <th><input type="text" class="search_input form-control" name="order_sn" value="<?php echo $filter['order_sn']; ?>" /></th>
                                <th><input type="text" class="search_input form-control" name="lock_sn" value="<?php echo $filter['lock_sn']; ?>" /></th>
                                <th><input type="text" class="search_input form-control" name="bicycle_sn" value="<?php echo $filter['bicycle_sn']; ?>" /></th>
                                <th><input type="text" class="search_input form-control" name="user_name" value="<?php echo $filter['user_name']; ?>" /></th>
                                <th><input type="text" class="search_input form-control" name="region_name" value="<?php echo $filter['region_name']; ?>" /></th>
                                <th>
                                    <select class="search_input form-control" name="order_state">
                                        <option value>全选</option>
                                        <?php foreach($order_state as $k => $v) { ?>
                                        <option value="<?php echo $k; ?>" <?php echo (string)$k == $filter['order_state'] ? 'selected' : ''; ?>><?php echo $v; ?></option>
                                        <?php } ?>
                                    </select>
                                </th>
                                <th><input type="text" class="search_input form-control date-range" name="add_time" value="<?php echo $filter['add_time']; ?>" /></th>
                                <th>
                                    <!--<div class="btn-group">
                                        <button type="submit" class="btn btn-info" id="submit-btn">
                                            <i class="fa fa-fw fa-search"></i>搜索
                                        </button>
                                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="#" class="page-excel-data">导出当前页(Excel)</a></li>
                                            <li><a href="#" class="excel-data">导出全部(Excel)</a></li>
                                            <li class="divider"></li>
                                            <li><a href="#" class="page-pdf-data">导出当前页(PDF)</a></li>
                                            <li><a href="#" class="pdf-data">导出全部(PDF)</a></li>
                                        </ul>
                                    </div>-->
                                    <button type="submit" class="btn btn-info" id="submit-btn">
                                        <i class="fa fa-fw fa-search"></i>搜索
                                    </button>
                                </th>
                            </thead>
                            <tbody>
                            <?php foreach ($data_rows as $data) { ?>
                            <tr>
                                <td><?php echo $data['order_sn']?></td>
                                <td><?php echo $data['lock_sn']?></td>
                                <td><?php echo $data['bicycle_sn']?></td>
                                <td><?php echo $data['user_name']?></td>
                                <td><?php echo $data['region_name']?></td>
                                <td><?php echo $data['order_state']?></td>
                                <td><?php echo $data['add_time']?></td>
                                <td><button data-url="<?php echo $data['info_action']; ?>" type="button" class="btn btn-info link"><i class="fa fa-fw fa-eye"></i>查看</button></td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <div class="row"><div class="col-sm-6 text-left"><?php echo $pagination; ?></div></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

<script>
    $('.search_input').bind('keydown change', function (e) {
        var key = e.which;
        if (key == 13) {
            $('.search_form').submit();
        }
        if(e.type=='change'){
        }
    });

    $('.date-range').daterangepicker({
        locale:{
            format: 'YYYY-MM-DD',
            isAutoVal:false,
        }
    });

    $('.excel-data').on('click',function(){
        $('#page_export').val(0);
        var query=$('.search_form').serialize();
        if (confirm('确定要导出吗?')) {
            var url = '/orders/Index/carpool_orders_excel?' + query;
            $('#export-form').attr('action', url);
            $('#export-form').submit();
        }
    });

    $('.page-excel-data').on('click',function(){
        $('#page_export').val(1);
        var query=$('.search_form').serialize();
        if (confirm('确定要导出吗?')) {
            var url = '/orders/Index/carpool_orders_excel?' + query;
            $('#export-form').attr('action', url);
            $('#export-form').submit();
        }
    });

    $('.pdf-data').on('click',function(){
        $('#page_export').val(0);
        var query=$('.search_form').serialize();
        if (confirm('确定要导出吗?')) {
            var url = '/orders/Index/carpool_orders_pdf?' + query;
            $('#export-form').attr('action', url);
            $('#export-form').submit();
        }
    });

    $('.page-pdf-data').on('click',function(){
        $('#page_export').val(1);
        var query=$('.search_form').serialize();
        if (confirm('确定要导出吗?')) {
            var url = '/orders/Index/carpool_orders_pdf?' + query;
            $('#export-form').attr('action', url);
            $('#export-form').submit();
        }
    });

</script>
<?php echo $footer;?>