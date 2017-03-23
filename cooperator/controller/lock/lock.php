<?php
class ControllerLockLock extends Controller {
    private $cur_url = null;
    private $error = null;
    
    public function __construct($registry) {
        parent::__construct($registry);

        // 当前网址
        $this->cur_url = $this->url->link($this->request->get['route']);

        // 加载Lock Model
        $this->load->library('sys_model/lock', true);
    }

    /**
     * 锁列表
     */
    public function index() {
        $filter = $this->request->get(array('lock_sn', 'lock_name', 'battery', 'system_time', 'open_nums', 'lock_status'));

        $condition = array(
            'cooperator_id' => $this->logic_cooperator->getId()
        );
        if (!empty($filter['lock_sn'])) {
            $condition['lock_sn'] = array('like', "%{$filter['lock_sn']}%");
        }
        if (!empty($filter['lock_name'])) {
            $condition['lock_name'] = array('like', "%{$filter['lock_name']}%");
        }
        if (is_numeric($filter['battery'])) {
            $condition['battery'] = (int)$filter['battery'];
        }
        if (!empty($filter['system_time'])) {
            $system_time = explode(' 至 ', $filter['system_time']);
            $condition['system_time'] = array(
                array('gt', strtotime($system_time[0])),
                array('lt', bcadd(86399, strtotime($system_time[1])))
            );
        }
        if (is_numeric($filter['open_nums'])) {
            $condition['open_nums'] = (int)$filter['open_nums'];
        }
        if (is_numeric($filter['lock_status'])) {
            $condition['lock_status'] = (int)$filter['lock_status'];
        }

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        $order = array();

        $rows = $this->config->get('config_limit_admin');
        $offset = ($page - 1) * $rows;
        $limit = sprintf('%d, %d', $offset, $rows);

        $result = $this->sys_model_lock->getLockList($condition, $order, $limit);
        $total = $this->sys_model_lock->getTotalLocks($condition);

        $lock_status = get_lock_status();
        if (is_array($result) && !empty($result)) {
            foreach ($result as &$item) {
                $item['system_time'] = $item['system_time'] == 0 ? '没有更新过' : date('Y-m-d H:i:s', $item['system_time']);
                $item['lock_status'] = isset($lock_status[$item['lock_status']]) ? $lock_status[$item['lock_status']] : '';

                $item['edit_action'] = $this->url->link('lock/lock/edit', 'lock_sn='.$item['lock_sn']);
                $item['delete_action'] = $this->url->link('lock/lock/delete', 'lock_sn='.$item['lock_sn']);
                $item['info_action'] = $this->url->link('lock/lock/info', 'lock_sn='.$item['lock_sn']);
            }
        }

        $data_columns = $this->getDataColumns();
        $this->assign('data_columns', $data_columns);
        $this->assign('data_rows', $result);
        $this->assign('filter', $filter);
        $this->assign('lock_status', $lock_status);
        $this->assign('action', $this->cur_url);
        $this->assign('add_action', $this->url->link('lock/lock/add'));

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

        $this->response->setOutput($this->load->view('lock/lock_list', $this->output));
    }

    /**
     * 表格字段
     * @return mixed
     */
    protected function getDataColumns() {
        $this->setDataColumn('锁编号');
        $this->setDataColumn('锁名称');
        $this->setDataColumn('当前电量（百分比）');
        $this->setDataColumn('开锁次数');
        $this->setDataColumn('更新时间');
        $this->setDataColumn('状态');
        return $this->data_columns;
    }

    /**
     * 添加锁
     */
    public function add() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $input = $this->request->post(array('lock_sn', 'lock_name'));
            $data = array(
                'lock_sn' => $input['lock_sn'],
                'lock_name' => $input['lock_name'],
            );
            $this->sys_model_lock->addLock($data);

            $this->session->data['success'] = '添加锁成功！';
            
            $filter = $this->request->get(array('lock_sn', 'lock_name', 'battery', 'system_time', 'open_nums', 'lock_status'));

            $this->load->controller('common/base/redirect', $this->url->link('lock/lock', $filter, true));
        }

        $this->assign('title', '锁添加');
        $this->getForm();
    }

    /**
     * 编辑锁
     */
    public function edit() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $input = $this->request->post(array('lock_name'));
            $lock_sn = $this->request->get['lock_sn'];
            $data = array(
                'lock_name' => $input['lock_name']
            );
            $condition = array(
                'lock_sn' => $lock_sn
            );
            $this->sys_model_lock->updateLock($condition, $data);

            $this->session->data['success'] = '编辑锁成功！';

            $filter = $this->request->get(array('lock_sn', 'lock_name', 'battery', 'system_time', 'open_nums', 'lock_status'));

            $this->load->controller('common/base/redirect', $this->url->link('lock/lock', $filter, true));
        }

        $this->assign('title', '编辑锁');
        $this->getForm();
    }

    /**
     * 删除锁
     */
    public function delete() {
        if (isset($this->request->get['lock_sn']) && $this->validateDelete()) {
            $condition = array(
                'lock_sn' => $this->request->get['lock_sn']
            );
            $this->sys_model_lock->deleteLock($condition);

            $this->session->data['success'] = '删除锁成功！';
        }
        $filter = $this->request->get(array('lock_sn', 'lock_name', 'battery', 'system_time', 'open_nums', 'lock_status'));
        $this->load->controller('common/base/redirect', $this->url->link('lock/lock', $filter, true));
    }

    /**
     * 锁详情
     */
    public function info() {
        // 编辑时获取已有的数据
        $lock_sn = $this->request->get('lock_sn');
        $condition = array(
            'lock_sn' => $lock_sn
        );
        $info = $this->sys_model_lock->getLockInfo($condition);
        if (!empty($info)) {
            $lock_status = get_lock_status();
            $info['system_time'] = $info['system_time'] == 0 ? '没有更新过' : date('Y-m-d H:i:s', $info['system_time']);
            $info['lock_status'] = isset($lock_status[$info['lock_status']]) ? $lock_status[$info['lock_status']] : '';
            $info['gx'] = $info['gx'] * 0.01;
            $info['gy'] = $info['gy'] * 0.01;
            $info['gz'] = $info['gz'] * 0.01;
        }

        $this->assign('data', $info);

        $this->response->setOutput($this->load->view('lock/lock_info', $this->output));
    }

    private function getForm() {
        // 编辑时获取已有的数据
        $info = $this->request->post(array('lock_sn', 'lock_name'));
        $lock_sn = $this->request->get('lock_sn');
        if (isset($this->request->get['lock_sn']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $condition = array(
                'lock_sn' => $this->request->get['lock_sn']
            );
            $info = $this->sys_model_lock->getLockInfo($condition);
        }

        $this->assign('lock_sn', $lock_sn);
        $this->assign('data', $info);
        $this->assign('lock_status', get_lock_status());
        $this->assign('action', $this->cur_url . '&lock_sn=' . $lock_sn);
        $this->assign('error', $this->error);

        $this->response->setOutput($this->load->view('lock/lock_form', $this->output));
    }

    /**
     * 验证表单数据
     * @return bool
     */
    private function validateForm() {
        $input = $this->request->post(array('lock_name'));

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