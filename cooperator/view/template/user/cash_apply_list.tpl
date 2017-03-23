<?php echo $header;?>
<!-- Content Header (Page Header) -->
<section class="content-header">
    <h1>提现列表</h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i>首页</li>
        <li>提现列表</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <form action="" method="post" id="export-form" style="display: none;">
                        <input type="hidden" name="page_export" id="page_export">
                        <input type="hidden" name="current_page" id="current_page" value="1">
                    </form>
                </div>
                <form class="search_form" action="<?php echo $action; ?>" method="get">
                    <div class="box-body">
                        <?php if (isset($error['warning'])) { ?>
                        <div class="alert alert-danger"><i class="fa fa-exclamation"></i><?php echo $error['warning']; ?>
                            <button type="button" class="close" data-dismiss="alert">x</button>
                        </div>
                        <?php } ?>
                        <?php if (isset($error['warning'])) { ?>
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
                            <tr class="searchBar">
                                <th><input type="text" class="search_input form-control" name="pdc_sn" value="<?php echo $filter['pdc_sn']; ?>"></th>
                                <th><input type="text" class="search_input form-control" name="pdc_user_name" value="<?php echo $filter['pdc_user_name']; ?>"></th>
                                <th><input type="text" class="search_input form-control" name="pdr_sn" value=""></th>
                                <th><input type="text" class="search_input form-control" name="pdc_amount" value=""></th>
                                <th>
                                    <select name="pdc_payment_name" class="search_input form-control">
                                        <?php foreach ($payment_types as $payment_type) { ?>
                                        <option value="<?php echo $payment_type['code'];?>"><?php echo $payment_type['text']; ?></option>
                                        <?php } ?>
                                    </select>
                                </th>
                                <th><input type="text" class="search_input form-control" name="pdc_add_time" value=""></th>
                                <th>
                                    <select name="pdc_bank_name" class="search_input form-control">
                                    <?php foreach($payment_states as $state) { ?>
                                        <option value="<?php echo $state['value'];?>"><?php echo $state['text'];?></option>
                                    <?php }?>
                                    </select>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data_rows as $data) { ?>
                            <tr>
                                <td><?php echo $data['pdc_sn']?></td>
                                <td><?php echo $data['pdc_user_name']?></td>
                                <td><?php echo $data['pdr_sn']?></td>
                                <td><?php echo $data['pdc_amount']?></td>
                                <td><?php echo $data['pdc_payment_name']?></td>
                                <td><?php echo $data['pdc_add_time']?></td>
                                <td><?php echo $data['pdc_payment_state']?></td>
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
<?php echo $footer; ?>