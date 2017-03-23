<?php
class ControllerCooperatorAccount extends Controller {
    private $cur_url = null;
    private $error = null;
    
    public function __construct($registry) {
        parent::__construct($registry);

        // 当前网址
        $this->cur_url = $this->url->link($this->request->get['route']);

        // 加载admin Model
        $this->load->library('sys_model/admin', true);
    }

    /**
     * 操作员列表
     */
    public function index() {
        $cooperator_id = $this->request->get('cooperator_id');
        $filter = $this->request->get(array('admin_name'));

        $condition = array(
            'cooperator_id' => $cooperator_id
        );
        if (!empty($filter['admin_name'])) {
            $condition['admin_name'] = array('like', "%{$filter['admin_name']}%");
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

        $field = 'admin.*,rbac_role.role_name';

        $join = array(
            'rbac_role' => 'rbac_role.role_id=admin.role_id'
        );

        $result = $this->sys_model_admin->getAdminList($condition, $order, $limit, $field, $join);
        $total = $this->sys_model_admin->getTotalAdmins($condition);

        if (is_array($result) && !empty($result)) {
            foreach ($result as &$item) {
                $item['edit_action'] = $this->url->link('cooperator/account/edit', 'admin_id='.$item['admin_id']);
                $item['delete_action'] = $this->url->link('cooperator/account/delete', 'admin_id='.$item['admin_id']);
                $item['info_action'] = $this->url->link('cooperator/account/info', 'admin_id='.$item['admin_id']);
            }
        }

        $data_columns = $this->getDataColumns();
        $this->assign('data_columns', $data_columns);
        $this->assign('data_rows', $result);
        $this->assign('static', HTTP_CATALOG);
        $this->assign('filter', $filter);
        $this->assign('action', $this->cur_url);
        $this->assign('add_action', $this->url->link('cooperator/account/add'));
        $this->assign('cooperator_action', $this->url->link('cooperator/cooperator/info', 'cooperator_id='.$cooperator_id));
        $this->assign('region_action', $this->url->link('cooperator/region', 'cooperator_id='.$cooperator_id));
        $this->assign('bicycle_action', $this->url->link('cooperator/bicycle', 'cooperator_id='.$cooperator_id));
        $this->assign('role_action', $this->url->link('cooperator/role', 'cooperator_id='.$cooperator_id));

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

        $this->response->setOutput($this->load->view('cooperator/account_list', $this->output));
    }

    /**
     * 表格字段
     * @return mixed
     */
    protected function getDataColumns() {
        $this->setDataColumn('登录名');
        $this->setDataColumn('角色');
        $this->setDataColumn('景区管辖');
        return $this->data_columns;
    }

    /**
     * 添加操作员
     */
    public function add() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $input = $this->request->post(array('cooperator_name', 'password', 'region', 'state'));
            $now = time();
            $data = array(
                'cooperator_name' => $input['cooperator_name'],
                'password' => $input['password'],
                'state' => (int)$input['state'],
                'add_time' => $now
            );
            $cooperator_id = $this->sys_model_cooperator->addCooperator($data);
            if ($cooperator_id) {
                if (!empty($input['region']) && is_array($input['region'])) {
                    foreach ($input['region'] as $region_id) {
                        $data = array(
                            'cooperator_id' => $cooperator_id,
                            'region_id' => $region_id
                        );
                        $this->sys_model_cooperator->addCooperatorToRegion($data);
                    }
                }
            }

            $this->session->data['success'] = '添加操作员成功！';

            //加载管理员操作日志 model
            $this->load->library('sys_model/admin_log', true);
            $data = array(
                'admin_id' => $this->logic_admin->getId(),
                'admin_name' => $this->logic_admin->getadmin_name(),
                'log_description' => '添加操作员：' . $input['cooperator_name'],
                'log_ip' => $this->request->ip_address(),
                'log_time' => date('Y-m-d H:i:s')
            );
            $this->sys_model_admin_log->addAdminLog($data);

            $filter = $this->request->get(array('cooperator_name', 'login_time', 'login_ip', 'state'));

            $this->load->controller('common/base/redirect', $this->url->link('cooperator/cooperator', $filter, true));
        }

        $this->assign('title', '操作员添加');
        $this->getForm();
    }

    /**
     * 编辑操作员
     */
    public function edit() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $input = $this->request->post(array('password', 'region', 'state'));
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

            // 清空操作员所有的景区，重新绑定景区
            $condition = array(
                'cooperator_id' => $cooperator_id
            );
            $this->sys_model_cooperator->deleteCooperatorToRegion($condition);
            if (!empty($input['region']) && is_array($input['region'])) {
                foreach ($input['region'] as $region_id) {
                    $data = array(
                        'cooperator_id' => $cooperator_id,
                        'region_id' => $region_id
                    );
                    $this->sys_model_cooperator->addCooperatorToRegion($data);
                }
            }

            $this->session->data['success'] = '编辑操作员成功！';

            //加载管理员操作日志 model
            $this->load->library('sys_model/admin_log', true);
            $data = array(
                'admin_id' => $this->logic_admin->getId(),
                'admin_name' => $this->logic_admin->getadmin_name(),
                'log_description' => '编辑操作员：' . $cooperator_id,
                'log_ip' => $this->request->ip_address(),
                'log_time' => date('Y-m-d H:i:s')
            );
            $this->sys_model_admin_log->addAdminLog($data);

            $filter = $this->request->get(array('cooperator_name', 'login_time', 'login_ip', 'state'));

            $this->load->controller('common/base/redirect', $this->url->link('cooperator/cooperator', $filter, true));
        }

        $this->assign('title', '编辑操作员');
        $this->getForm();
    }

    /**
     * 删除操作员
     */
    public function delete() {
        if (isset($this->request->get['cooperator_id']) && $this->validateDelete()) {
            $condition = array(
                'cooperator_id' => $this->request->get['cooperator_id']
            );
            $this->sys_model_cooperator->deleteCooperator($condition);

            $this->session->data['success'] = '删除操作员成功！';

            //加载管理员操作日志 model
            $this->load->library('sys_model/admin_log', true);
            $data = array(
                'admin_id' => $this->logic_admin->getId(),
                'admin_name' => $this->logic_admin->getadmin_name(),
                'log_description' => '删除操作员：' . $this->request->get['cooperator_id'],
                'log_ip' => $this->request->ip_address(),
                'log_time' => date('Y-m-d H:i:s')
            );
            $this->sys_model_admin_log->addAdminLog($data);

        }
        $filter = $this->request->get(array('cooperator_name', 'login_time', 'login_ip', 'state'));
        $this->load->controller('common/base/redirect', $this->url->link('cooperator/cooperator', $filter, true));
    }

    /**
     * 操作员详情
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
        $this->assign('return_action', $this->url->link('cooperator/cooperator'));
        $this->assign('region_action', $this->url->link('cooperator/region', 'cooperator_id='.$cooperator_id));
        $this->assign('account_action', $this->url->link('cooperator/account', 'cooperator_id='.$cooperator_id));
        $this->response->setOutput($this->load->view('cooperator/cooperator_info', $this->output));
    }

    private function getForm() {
        // 编辑时获取已有的数据
        $info = $this->request->post(array('cooperator_name', 'password', 'confirm', 'region', 'state'));
        $cooperator_id = $this->request->get('cooperator_id');
        if (isset($this->request->get['cooperator_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $condition = array(
                'cooperator_id' => $this->request->get['cooperator_id']
            );
            $info = $this->sys_model_cooperator->getCooperatorInfo($condition);
        }


        if (empty($info['region'])) {
            $info['region'] = array();
            //  编辑时读取已选的景区
            if (isset($this->request->get['cooperator_id'])) {
                $condition = array(
                    'cooperator_id' => $this->request->get['cooperator_id']
                );
                $cooperatorRegion = $this->sys_model_cooperator->getCooperatorToRegionList($condition);
                if (is_array($cooperatorRegion) && !empty($cooperatorRegion)) {
                    foreach ($cooperatorRegion as $val) {
                        $info['region'][] = $val['region_id'];
                    }
                }
            }
        }

        // 加载景区 model
        $this->load->library('sys_model/region', true);
        $regions = $this->sys_model_region->getRegionList();

        $this->assign('cooperator_id', $cooperator_id);
        $this->assign('data', $info);
        $this->assign('regions', $regions);
        $this->assign('return_action', $this->url->link('cooperator/cooperator'));
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

    /**
     * 把指定角色的权限数据添加到权限树数据中
     * @param $list
     * @param $selected
     * @return array
     */
    private function _getTreeData($list, $selected) {
        $role_permission = array();
        foreach ($list as $item) {
            $role_permission[] = array(
                'id' => $item['permission_id'] + 0,
                'pId' => $item['permission_parent_id'] + 0,
                'name' => $item['permission_name'],
                'open' => false,
                'checked' => in_array($item['permission_id'], $selected)
            );
        }
        return $role_permission;
    }
}