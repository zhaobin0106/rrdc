<?php
class ControllerUserRecharge extends Controller {
    private $cur_url = null;
    private $error = null;
    
    public function __construct($registry) {
        parent::__construct($registry);
        $this->language->load('bicycle/bicycle');
        $languages = $this->language->all();
        $this->assign('languages',$languages);

        // 当前网址
        $this->cur_url = $this->url->link($this->request->get['route']);

        // 加载bicycle Model
        $this->load->library('sys_model/deposit', true);
    }

    /**
     * 充值记录列表
     */
    public function index() {
        $filter = $this->request->get(array('filter_type', 'pdr_sn', 'mobile', 'pdr_amount', 'pdr_type', 'pdr_payment_state', 'pdr_admin', 'pdr_add_time'));

        $condition = array();
        if (!empty($filter['pdr_sn'])) {
            $condition['pdr_sn'] = array('like', "%{$filter['pdr_sn']}%");
        }
        if (!empty($filter['mobile'])) {
            $condition['mobile'] = array('like', "%{$filter['mobile']}%");
        }
        if (is_numeric($filter['pdr_amount'])) {
            $condition['pdr_amount'] = (float)$filter['pdr_amount'];
        }
        if (is_numeric($filter['pdr_type'])) {
            $condition['pdr_type'] = (int)$filter['pdr_type'];
        }
        if (is_numeric($filter['pdr_payment_state'])) {
            $condition['pdr_payment_state'] = (int)$filter['pdr_payment_state'];
        }
        if (!empty($filter['pdr_admin'])) {
            $condition['pdr_admin'] = array('like', "%{$filter['pdr_admin']}%");
        }
        if (!empty($filter['pdr_add_time'])) {
            $pdr_add_time = explode(' 至 ', $filter['pdr_add_time']);
            $condition['pdr_add_time'] = array(
                array('gt', strtotime($pdr_add_time[0])),
                array('lt', bcadd(86399, strtotime($pdr_add_time[1])))
            );
        }

        $filter_types = array(
            'pdr_sn' => '订单号',
            'mobile' => '手机号',
            'pdr_amount' => '充值金额',
            'pdr_admin' => '管理员名称',
        );
        $filter_type = $this->request->get('filter_type');
        if (empty($filter_type)) {
            reset($filter_types);
            $filter_type = key($filter_types);
        }

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        $order = 'pdr_add_time DESC';
        $rows = $this->config->get('config_limit_admin');
        $offset = ($page - 1) * $rows;
        $limit = sprintf('%d, %d', $offset, $rows);

        $result = $this->sys_model_deposit->getRechargeList($condition, '*', $order, $limit);
        $total = $this->sys_model_deposit->getRechargeCount($condition);

        $recharge_type = get_recharge_type();
        $payment_state = get_payment_state();

        if (is_array($result) && !empty($result)) {
            foreach ($result as &$item) {
                $item['pdr_type'] = isset($recharge_type[$item['pdr_type']]) ? $recharge_type[$item['pdr_type']] : '';
                $item['pdr_payment_state'] = isset($payment_state[$item['pdr_payment_state']]) ? $payment_state[$item['pdr_payment_state']] : '';

                $item['pdr_add_time'] = date('Y-m-d H:i:s', $item['pdr_add_time']);
                $item['edit_action'] = $this->url->link('user/recharge/edit', 'pdr_id='.$item['pdr_id']);
                $item['delete_action'] = $this->url->link('user/recharge/delete', 'pdr_id='.$item['pdr_id']);
                $item['info_action'] = $this->url->link('user/recharge/info', 'pdr_id='.$item['pdr_id']);
            }
        }

        $data_columns = $this->getDataColumns();
        $this->assign('data_columns', $data_columns);
        $this->assign('data_rows', $result);
        $this->assign('filter', $filter);
        $this->assign('filter_type', $filter_type);
        $this->assign('filter_types', $filter_types);
        $this->assign('pdr_types', $recharge_type);
        $this->assign('payment_states', $payment_state);
        $this->assign('action', $this->cur_url);
        $this->assign('add_action', $this->url->link('user/recharge/add'));

        if (isset($this->session->data['success'])) {
            $this->assign('success', $this->session->data['success']);
            unset($this->session->data['success']);
        }

        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $page;
        $pagination->page_size = $rows;
        $pagination->url = $this->cur_url . '&amp;page={page}' . '&amp;' . str_replace('&', '&amp;', http_build_query($filter));
        $pagination = $pagination->render();
        $results = sprintf($this->language->get('text_pagination'), ($total) ? $offset + 1 : 0, ($offset > ($total - $rows)) ? $total : ($offset + $rows), $total, ceil($total / $rows));

        $this->assign('pagination', $pagination);
        $this->assign('results', $results);

        $this->assign('export_action', $this->url->link('user/recharge/export'));

        $this->response->setOutput($this->load->view('user/recharge_list', $this->output));
    }

    /**
     * 表格字段
     * @return mixed
     */
    protected function getDataColumns() {
        $this->setDataColumn('订单号');
        $this->setDataColumn('手机号');
        $this->setDataColumn('充值金额');
        $this->setDataColumn('充值类型');
        $this->setDataColumn('支付状态');
        $this->setDataColumn('管理员名称');
        $this->setDataColumn('下单时间');
        return $this->data_columns;
    }

    /**
     * 添加充值记录
     */
    public function add() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $input = $this->request->post(array('bicycle_sn', 'type', 'lock_sn'));
            $now = time();
            $data = array(
                'bicycle_sn' => $input['bicycle_sn'],
                'type' => (int)$input['type'],
                'lock_sn' => $input['lock_sn'],
                'add_time' => $now
            );
            $this->sys_model_bicycle->addBicycle($data);

            $this->session->data['success'] = '添加充值记录成功！';
            
            $filter = $this->request->get(array('filter_type', 'bicycle_sn', 'type', 'lock_sn', 'is_using'));

            $this->load->controller('common/base/redirect', $this->url->link('user/recharge', $filter, true));
        }

        $this->assign('title', '充值记录添加');
        $this->getForm();
    }

    /**
     * 编辑充值记录
     */
    public function edit() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $input = $this->request->post(array('bicycle_sn', 'type', 'lock_sn'));
            $bicycle_id = $this->request->get['bicycle_id'];
            $data = array(
                'bicycle_sn' => $input['bicycle_sn'],
                'type' => (int)$input['type'],
                'lock_sn' => $input['lock_sn']
            );
            $condition = array(
                'bicycle_id' => $bicycle_id
            );
            $this->sys_model_bicycle->updateBicycle($condition, $data);

            $this->session->data['success'] = '编辑充值记录成功！';

            $filter = $this->request->get(array('filter_type', 'bicycle_sn', 'type', 'lock_sn', 'is_using'));

            $this->load->controller('common/base/redirect', $this->url->link('user/recharge', $filter, true));
        }

        $this->assign('title', '编辑充值记录');
        $this->getForm();
    }

    /**
     * 删除充值记录
     */
    public function delete() {
        if (isset($this->request->get['bicycle_id']) && $this->validateDelete()) {
            $condition = array(
                'bicycle_id' => $this->request->get['bicycle_id']
            );
            $this->sys_model_bicycle->deleteBicycle($condition);

            $this->session->data['success'] = '删除充值记录成功！';
        }
        $filter = $this->request->get(array('filter_type', 'bicycle_sn', 'type', 'lock_sn', 'is_using'));
        $this->load->controller('common/base/redirect', $this->url->link('user/recharge', $filter, true));
    }

    /**
     * 充值记录详情
     */
    public function info() {
        // 编辑时获取已有的数据
        $pdr_id = $this->request->get('pdr_id');
        $condition = array(
            'pdr_id' => $pdr_id
        );
        $info = $this->sys_model_deposit->getRechargeInfo($condition, '*');
        if (!empty($info)) {
            $model = array(
                'pdr_type' => get_recharge_type(),
                'pdr_payment_state' => get_recharge_type()
            );
            foreach ($model as $k => $v) {
                $info[$k] = isset($v[$info[$k]]) ? $v[$info[$k]] : '';
            }
            $info['pdr_add_time'] = (isset($info['pdr_add_time']) && !empty($info['pdr_add_time'])) ? date('Y-m-d H:i:s', $info['pdr_add_time']) : '';
            $info['pdr_payment_time'] = (isset($info['pdr_payment_time']) && !empty($info['pdr_payment_time'])) ? date('Y-m-d H:i:s', $info['pdr_payment_time']) : '';
        }

        $this->assign('data', $info);
        $this->assign('return_action', $this->url->link('user/recharge'));

        $this->response->setOutput($this->load->view('user/recharge_info', $this->output));
    }

    /**
     * 导出
     */
    public function export() {
        $filter = $this->request->post(array('filter_type', 'pdr_sn', 'mobile', 'pdr_amount', 'pdr_type', 'pdr_payment_state', 'pdr_admin', 'pdr_add_time'));

        $condition = array();
        if (!empty($filter['pdr_sn'])) {
            $condition['pdr_sn'] = array('like', "%{$filter['pdr_sn']}%");
        }
        if (!empty($filter['mobile'])) {
            $condition['mobile'] = array('like', "%{$filter['mobile']}%");
        }
        if (is_numeric($filter['pdr_amount'])) {
            $condition['pdr_amount'] = (float)$filter['pdr_amount'];
        }
        if (is_numeric($filter['pdr_type'])) {
            $condition['pdr_type'] = (int)$filter['pdr_type'];
        }
        if (is_numeric($filter['pdr_payment_state'])) {
            $condition['pdr_payment_state'] = (int)$filter['pdr_payment_state'];
        }
        if (!empty($filter['pdr_admin'])) {
            $condition['pdr_admin'] = array('like', "%{$filter['pdr_admin']}%");
        }
        if (!empty($filter['pdr_add_time'])) {
            $pdr_add_time = explode(' 至 ', $filter['pdr_add_time']);
            $condition['pdr_add_time'] = array(
                array('gt', strtotime($pdr_add_time[0])),
                array('lt', bcadd(86399, strtotime($pdr_add_time[1])))
            );
        }
        $order = 'pdr_add_time DESC';
        $limit = '';

        $result = $this->sys_model_deposit->getRechargeList($condition, '*', $order, $limit);
        $list = array();
        if (is_array($result) && !empty($result)) {
            $recharge_type = get_recharge_type();
            $payment_state = get_payment_state();
            foreach ($result as $v) {
                $list[] = array(
                    'pdr_sn' => $v['pdr_sn'],
                    'pdr_user_name' => $v['pdr_user_name'],
                    'pdr_amount' => $v['pdr_amount'],
                    'pdr_type' => $recharge_type[$v['pdr_type']],
                    'pdr_payment_state' => $payment_state[$v['pdr_payment_state']],
                    'pdr_admin' => $v['pdr_admin'],
                    'pdr_add_time' => date("Y-m-d H:m:s",$v['add_time']),
                );
            }
        }

        $data = array(
            'title' => '充值记录列表',
            'header' => array(
                'pdr_sn' => '订单号',
                'pdr_user_name' => '手机号',
                'pdr_amount' => '充值金额',
                'pdr_type' => '充值类型',
                'pdr_payment_state' => '支付状态',
                'pdr_admin' => '管理员名称',
                'pdr_add_time' => '下单时间',
            ),
            'list' => $list
        );
        $this->load->controller('common/base/exportExcel', $data);
    }

    private function getForm() {
        // 编辑时获取已有的数据
        $info = $this->request->post(array('bicycle_sn', 'type', 'lock_sn'));
        $bicycle_id = $this->request->get('bicycle_id');
        if (isset($this->request->get['bicycle_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $condition = array(
                'bicycle_id' => $this->request->get['bicycle_id']
            );
            $info = $this->sys_model_bicycle->getBicycleInfo($condition);
        }

        $this->assign('data', $info);
        $this->assign('types', get_bicycle_type());
        $this->assign('action', $this->cur_url . '&bicycle_id=' . $bicycle_id);
        $this->assign('error', $this->error);

        $this->response->setOutput($this->load->view('user/recharge_form', $this->output));
    }

    /**
     * 验证表单数据
     * @return bool
     */
    private function validateForm() {
        $input = $this->request->post(array('bicycle_sn', 'type', 'lock_sn'));

        foreach ($input as $k => $v) {
            if (empty($v)) {
                $this->error[$k] = '请输入完整！';
            }
        }

        if ($this->error) {
            $this->error['warning'] = '警告: 存在错误，请检查！';
        }
        return !$this->error;
    }

    /**
     * 验证删除条件
     */
    private function validateDelete() {
        return !$this->error;
    }
}