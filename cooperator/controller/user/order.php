<?php
class ControllerUserOrder extends Controller {
    private $cooperator_id = null;
    private $cur_url = null;
    private $error = null;
    
    public function __construct($registry) {
        parent::__construct($registry);

        // 当前网址
        $this->cur_url = $this->url->link($this->request->get['route']);
        $this->cooperator_id = $this->logic_admin->getParam('cooperator_id');

        // 加载bicycle Model
        $this->load->library('sys_model/orders', true);
    }

    /**
     * 消费记录列表
     */
    public function index() {
        $filter = $this->request->get(array('filter_type', 'order_sn', 'lock_sn', 'bicycle_sn', 'user_name', 'region_name', 'order_state', 'add_time'));

        $condition = array(
            'cooperator_id' => $this->cooperator_id
        );
        if (!empty($filter['order_sn'])) {
            $condition['order_sn'] = array('like', "%{$filter['order_sn']}%");
        }
        if (!empty($filter['lock_sn'])) {
            $condition['lock_sn'] = array('like', "%{$filter['lock_sn']}%");
        }
        if (!empty($filter['bicycle_sn'])) {
            $condition['bicycle_sn'] = array('like', "%{$filter['bicycle_sn']}%");
        }
        if (!empty($filter['user_name'])) {
            $condition['user_name'] = array('like', "%{$filter['user_name']}%");
        }
        if (!empty($filter['region_name'])) {
            $condition['region_name'] = array('like', "%{$filter['region_name']}%");
        }
        if (is_numeric($filter['order_state'])) {
            $condition['order_state'] = (int)$filter['order_state'];
        }
        if (!empty($filter['add_time'])) {
            $pdr_add_time = explode(' 至 ', $filter['add_time']);
            $condition['add_time'] = array(
                array('gt', strtotime($pdr_add_time[0])),
                array('lt', bcadd(86399, strtotime($pdr_add_time[1])))
            );
        }

        $filter_types = array(
            'order_sn' => '订单sn',
            'lock_sn' => '锁sn',
            'bicycle_sn' => '单车sn',
            'user_name' => '手机号',
            'region_name' => '区域',
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

        $order = 'add_time DESC';
        $rows = $this->config->get('config_limit_admin');
        $offset = ($page - 1) * $rows;
        $limit = sprintf('%d, %d', $offset, $rows);

        $result = $this->sys_model_orders->getOrdersList($condition, $order, $limit);
        $total = $this->sys_model_orders->getTotalOrders($condition);

        $order_state = get_order_state();

        if (is_array($result) && !empty($result)) {
            foreach ($result as &$item) {
                $item['order_state'] = isset($order_state[$item['order_state']]) ? $order_state[$item['order_state']] : '';
                $item['add_time'] = !empty($item['add_time']) ? date('Y-m-d H:i:s', $item['add_time']) : '';

                $item['edit_action'] = $this->url->link('user/order/edit', 'order_id='.$item['order_id']);
                $item['delete_action'] = $this->url->link('user/order/delete', 'order_id='.$item['order_id']);
                $item['info_action'] = $this->url->link('user/order/info', 'order_id='.$item['order_id']);
            }
        }

        $data_columns = $this->getDataColumns();
        $this->assign('data_columns', $data_columns);
        $this->assign('data_rows', $result);
        $this->assign('filter', $filter);
        $this->assign('filter_type', $filter_type);
        $this->assign('filter_types', $filter_types);
        $this->assign('order_state', $order_state);
        $this->assign('action', $this->cur_url);
        $this->assign('add_action', $this->url->link('user/order/add'));
        $this->assign('chart_action', $this->url->link('user/order/chart'));

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

        $this->assign('export_action', $this->url->link('user/order/export'));

        $this->response->setOutput($this->load->view('user/order_list', $this->output));
    }

    /**
     * 表格字段
     * @return mixed
     */
    protected function getDataColumns() {
        $this->setDataColumn('订单sn');
        $this->setDataColumn('锁sn');
        $this->setDataColumn('单车sn');
        $this->setDataColumn('手机号');
        $this->setDataColumn('区域');
        $this->setDataColumn('状态');
        $this->setDataColumn('下单时间');
        return $this->data_columns;
    }

    /**
     * 添加消费记录
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

            $this->session->data['success'] = '添加消费记录成功！';

            $filter = $this->request->get(array('filter_type', 'bicycle_sn', 'type', 'lock_sn', 'is_using'));

            $this->load->controller('common/base/redirect', $this->url->link('user/order', $filter, true));
        }

        $this->assign('title', '消费记录添加');
        $this->getForm();
    }

    /**
     * 编辑消费记录
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

            $this->session->data['success'] = '编辑消费记录成功！';

            $filter = $this->request->get(array('filter_type', 'bicycle_sn', 'type', 'lock_sn', 'is_using'));

            $this->load->controller('common/base/redirect', $this->url->link('user/order', $filter, true));
        }

        $this->assign('title', '编辑消费记录');
        $this->getForm();
    }

    /**
     * 删除消费记录
     */
    public function delete() {
        if (isset($this->request->get['bicycle_id']) && $this->validateDelete()) {
            $condition = array(
                'bicycle_id' => $this->request->get['bicycle_id']
            );
            $this->sys_model_bicycle->deleteBicycle($condition);

            $this->session->data['success'] = '删除消费记录成功！';
        }
        $filter = $this->request->get(array('filter_type', 'bicycle_sn', 'type', 'lock_sn', 'is_using'));
        $this->load->controller('common/base/redirect', $this->url->link('user/order', $filter, true));
    }

    /**
     * 消费记录详情
     */
    public function info() {
        // 编辑时获取已有的数据
        $order_id = $this->request->get('order_id');
        $condition = array(
            'order_id' => $order_id
        );
        $info = $this->sys_model_orders->getOrdersInfo($condition);
        if (!empty($info)) {
            $info['order_status'] = $info['order_state'];
            $model = array(
                'order_state' => get_order_state()
            );
            foreach ($model as $k => $v) {
                $info[$k] = isset($v[$info[$k]]) ? $v[$info[$k]] : '';
            }

            $info['add_time'] = (isset($info['add_time']) && !empty($info['add_time'])) ? date('Y-m-d H:i:s', $info['add_time']) : '';
            $info['start_time'] = (isset($info['start_time']) && !empty($info['start_time'])) ? date('Y-m-d H:i:s', $info['start_time']) : '';
            $info['end_time'] = (isset($info['end_time']) && !empty($info['end_time'])) ? date('Y-m-d H:i:s', $info['end_time']) : '';
            $info['pdr_payment_time'] = (isset($info['pdr_payment_time']) && !empty($info['pdr_payment_time'])) ? date('Y-m-d H:i:s', $info['pdr_payment_time']) : '';
        }

        $this->assign('data', $info);
        $this->assign('return_action', $this->url->link('user/order'));

        $this->response->setOutput($this->load->view('user/order_info', $this->output));
    }

    /**
     * 统计图表
     */
    public function chart() {
        $filter = $this->request->get(array('add_time'));
        $condition = array();
        if (!empty($filter['add_time'])) {
            $pdr_add_time = explode(' 至 ', $filter['add_time']);

            $firstday = strtotime($pdr_add_time[0]);
            $lastday  = bcadd(86399, strtotime($pdr_add_time[1]));
            $condition['add_time'] = array(
                array('egt', $firstday),
                array('elt', $lastday)
            );
        } else {
            $firstday = strtotime(date('Y-m-01'));
            $lastday  = bcadd(86399, strtotime(date('Y-m-d')));
            $condition['add_time'] = array(
                array('egt', $firstday),
                array('elt', $lastday)
            );
        }
        // 初始化订单统计数据
        $dailyAmount = array();
        while ($firstday <= $lastday) {
            $tempDay = date('Y-m-d', $firstday);
            $dailyAmount[$tempDay] = 0;
            $firstday = strtotime('+1 day', $firstday);
        }

        $order = 'add_time DESC';
        $result = $this->sys_model_orders->getOrdersList($condition, $order);
        if (is_array($result) && !empty($result)) {
            foreach ($result as $item) {
                $tempDay = date('Y-m-d', $item['add_time']);
                $dailyAmount[$tempDay] += $item['pay_amount'];
            }
        }

        $orderData = array();
        $orderTotal = 0;
        if (is_array($dailyAmount) && !empty($dailyAmount)) {
            foreach ($dailyAmount as $key => $val) {
                $orderData[] = array(
                    'date' => $key,
                    'amount' => $val
                );
                $orderTotal += $val;
            }
        }
        $orderData = json_encode($orderData);
        $orderTotal = sprintf('%0.2f', $orderTotal);

        $this->assign('filter', $filter);
        $this->assign('orderData', $orderData);
        $this->assign('orderTotal', $orderTotal);
        $this->assign('action', $this->cur_url);
        $this->assign('index_action', $this->url->link('user/order'));

        $this->response->setOutput($this->load->view('user/order_chart', $this->output));
    }

    /**
     * 导出
     */
    public function export() {
        $filter = $this->request->post(array('filter_type', 'order_sn', 'lock_sn', 'bicycle_sn', 'user_name', 'region_name', 'order_state', 'add_time'));

        $condition = array();
        if (!empty($filter['order_sn'])) {
            $condition['order_sn'] = array('like', "%{$filter['order_sn']}%");
        }
        if (!empty($filter['lock_sn'])) {
            $condition['lock_sn'] = array('like', "%{$filter['lock_sn']}%");
        }
        if (!empty($filter['bicycle_sn'])) {
            $condition['bicycle_sn'] = array('like', "%{$filter['bicycle_sn']}%");
        }
        if (!empty($filter['user_name'])) {
            $condition['user_name'] = array('like', "%{$filter['user_name']}%");
        }
        if (!empty($filter['region_name'])) {
            $condition['region_name'] = array('like', "%{$filter['region_name']}%");
        }
        if (is_numeric($filter['order_state'])) {
            $condition['order_state'] = (int)$filter['order_state'];
        }
        if (!empty($filter['add_time'])) {
            $pdr_add_time = explode(' 至 ', $filter['add_time']);
            $condition['add_time'] = array(
                array('gt', strtotime($pdr_add_time[0])),
                array('lt', bcadd(86399, strtotime($pdr_add_time[1])))
            );
        }
        $order = 'add_time DESC';
        $limit = '';

        $result = $this->sys_model_orders->getOrdersList($condition, $order, $limit);
        $list = array();
        if (is_array($result) && !empty($result)) {
            $order_state = get_order_state();
            foreach ($result as $v) {
                $list[] = array(
                    'order_sn' => $v['order_sn'],
                    'lock_sn' => $v['lock_sn'],
                    'bicycle_sn' => $v['bicycle_sn'],
                    'user_name' => $v['user_name'],
                    'region_name' => $v['region_name'],
                    'order_state' =>$order_state[$v['order_state']],
                    'add_time' => date("Y-m-d h:m:s",$v['add_time']),
                );
            }
        }

        $data = array(
            'title' => '消费记录列表',
            'header' => array(
                'order_sn' => '订单sn',
                'lock_sn' => '锁sn',
                'bicycle_sn' => '单车sn',
                'user_name' => '手机号',
                'region_name' => '区域',
                'order_state' => '	状态',
                'add_time' => '下单时间',
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

        $this->response->setOutput($this->load->view('user/order_form', $this->output));
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