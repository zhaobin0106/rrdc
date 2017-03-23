<?php
class ControllerCooperatorCooperator extends Controller {
    private $cur_url = null;
    private $error = null;
    
    public function __construct($registry) {
        parent::__construct($registry);

        // 当前网址
        $this->cur_url = $this->url->link($this->request->get['route']);

        // 加载cooperator Model
        $this->load->library('sys_model/cooperator', true);
    }

    /**
     * 合伙人列表
     */
    public function index() {
        $filter = $this->request->get(array('cooperator_name', 'login_time', 'login_ip', 'state'));

        $condition = array();
        if (!empty($filter['cooperator_name'])) {
            $condition['cooperator_name'] = array('like', "%{$filter['cooperator_name']}%");
        }
        if (!empty($filter['login_time'])) {
            $login_time = explode(' 至 ', $filter['login_time']);
            $condition['login_time'] = array(
                array('gt', strtotime($login_time[0])),
                array('lt', bcadd(86399, strtotime($login_time[1])))
            );
        }
        if (!empty($filter['login_ip'])) {
            $condition['login_ip'] = array('like', "%{$filter['login_ip']}%");
        }
        if (is_numeric($filter['state'])) {
            $condition['state'] = (int)$filter['state'];
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

        $result = $this->sys_model_cooperator->getCooperatorList($condition, $order, $limit);
        $total = $this->sys_model_cooperator->getTotalCooperators($condition);

        $state = get_cooperator_state();
        if (is_array($result) && !empty($result)) {
            foreach ($result as &$item) {
                $item['state'] = isset($state[$item['state']]) ? $state[$item['state']] : '';
                $item['login_time'] = !empty($item['login_time']) ? date('Y-m-d H:i:s', $item['login_time']) : '';

                $item['edit_action'] = $this->url->link('cooperator/cooperator/edit', 'cooperator_id='.$item['cooperator_id']);
                $item['delete_action'] = $this->url->link('cooperator/cooperator/delete', 'cooperator_id='.$item['cooperator_id']);
                $item['info_action'] = $this->url->link('cooperator/cooperator/info', 'cooperator_id='.$item['cooperator_id']);
            }
        }

        $data_columns = $this->getDataColumns();
        $this->assign('data_columns', $data_columns);
        $this->assign('data_rows', $result);
        $this->assign('state', $state);
        $this->assign('filter', $filter);
        $this->assign('action', $this->cur_url);
        $this->assign('add_action', $this->url->link('cooperator/cooperator/add'));

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

        $this->response->setOutput($this->load->view('cooperator/cooperator_list', $this->output));
    }

    /**
     * 表格字段
     * @return mixed
     */
    protected function getDataColumns() {
        $this->setDataColumn('用户名称');
        $this->setDataColumn('最后登录时间');
        $this->setDataColumn('最后登录IP');
        $this->setDataColumn('状态');
        return $this->data_columns;
    }

    /**
     * 添加合伙人
     */
    public function add() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $input = $this->request->post(array('cooperator_name', 'password', 'state'));
            $now = time();
            $data = array(
                'cooperator_name' => $input['cooperator_name'],
                'password' => $input['password'],
                'state' => (int)$input['state'],
                'add_time' => $now
            );
            $this->sys_model_cooperator->addCooperator($data);

            $this->session->data['success'] = '添加合伙人成功！';

            $filter = $this->request->get(array('cooperator_name', 'login_time', 'login_ip', 'state'));

            $this->load->controller('common/base/redirect', $this->url->link('cooperator/cooperator', $filter, true));
        }

        $this->assign('title', '合伙人添加');
        $this->getForm();
    }

    /**
     * 编辑合伙人
     */
    public function edit() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $input = $this->request->post(array('password', 'state'));
            $cooperator_id = $this->request->get['cooperator_id'];
            $data = array(
                'state' => $input['state']
            );
            if (!empty($input['password'])) {
                $data['password'] = $input['password'];
            }
            $condition = array(
                'cooperator_id' => $cooperator_id
            );
            $this->sys_model_cooperator->updateCooperator($condition, $data);

            $this->session->data['success'] = '编辑合伙人成功！';

            $filter = $this->request->get(array('cooperator_name', 'login_time', 'login_ip', 'state'));

            $this->load->controller('common/base/redirect', $this->url->link('cooperator/cooperator', $filter, true));
        }

        $this->assign('title', '编辑合伙人');
        $this->getForm();
    }

    /**
     * 删除合伙人
     */
    public function delete() {
        if (isset($this->request->get['cooperator_id']) && $this->validateDelete()) {
            $condition = array(
                'cooperator_id' => $this->request->get['cooperator_id']
            );
            $this->sys_model_cooperator->deleteCooperator($condition);

            $this->session->data['success'] = '删除合伙人成功！';
        }
        $filter = $this->request->get(array('cooperator_name', 'login_time', 'login_ip', 'state'));
        $this->load->controller('common/base/redirect', $this->url->link('cooperator/cooperator', $filter, true));
    }

    /**
     * 合伙人详情
     */
    public function info() {
        // 编辑时获取已有的数据
        $cooperator_id = $this->request->get('cooperator_id');
        $condition = array(
            'cooperator_id' => $cooperator_id
        );
        $info = $this->sys_model_cooperator->getCooperatorInfo($condition);
        if (!empty($info)) {
            $state = get_cooperator_state();
            $info['state'] = isset($state[$info['state']]) ? $state[$info['state']] : '';
            $info['login_time'] = !empty($info['login_time']) ? date('Y-m-d H:i:s', $info['login_time']) : '';
            $info['add_time'] = !empty($info['add_time']) ? date('Y-m-d H:i:s', $info['add_time']) : '';
        }

        $this->assign('data', $info);

        $this->response->setOutput($this->load->view('cooperator/cooperator_info', $this->output));
    }

    private function getForm() {
        // 编辑时获取已有的数据
        $info = $this->request->post(array('cooperator_name', 'password', 'confirm', 'state'));
        $cooperator_id = $this->request->get('cooperator_id');
        if (isset($this->request->get['cooperator_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $condition = array(
                'cooperator_id' => $this->request->get['cooperator_id']
            );
            $info = $this->sys_model_cooperator->getCooperatorInfo($condition);
        }

        $this->assign('cooperator_id', $cooperator_id);
        $this->assign('data', $info);
        $this->assign('action', $this->cur_url . '&cooperator_id=' . $cooperator_id);
        $this->assign('error', $this->error);
        $this->assign('static', HTTP_IMAGE);

        $this->response->setOutput($this->load->view('cooperator/cooperator_form', $this->output));
    }

    /**
     * 验证表单数据
     * @return bool
     */
    private function validateForm() {
//        $input = $this->request->post(array('state', 'type', 'lock_sn'));
//
//        foreach ($input as $k => $v) {
//            if (empty($v)) {
//                $this->error[$k] = '请输入完整！';
//            }
//        }
        $route = $this->request->get('route');
        $password = $this->request->post('password');
        $confirm = $this->request->post('confirm');
        if ($route == 'cooperator/cooperator/add') {
            if (empty($password)) {
                var_dump($this->request->post);
                $this->error['password'] = '请输入密码！';
            }
        }
        if (!empty($password)) {
            if ($password !== $confirm) {
                $this->error['confirm'] = '两次输入密码不正确！';
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