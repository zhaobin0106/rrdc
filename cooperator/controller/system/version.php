<?php
class ControllerSystemVersion extends Controller {
    private $cur_url = null;
    private $error = null;
    
    public function __construct($registry) {
        parent::__construct($registry);

        // 当前网址
        $this->cur_url = $this->url->link($this->request->get['route']);

        // 加载 Model
        $this->load->library('sys_model/version', true);
    }

    /**
     * 版本列表
     */
    public function index() {

        $condition = array();

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        $state = get_setting_boolean();
        
        $order = 'add_time DESC';
        $rows = $this->config->get('config_limit_admin');
        $offset = ($page - 1) * $rows;
        $limit = sprintf('%d, %d', $offset, $rows);

        $result = $this->sys_model_version->getVersionList($condition, $order, $limit);
        $total = $this->sys_model_version->getTotalVersions($condition);

        if (is_array($result) && !empty($result)) {
            foreach ($result as &$item) {
                $item['add_time'] = !empty($item['add_time']) ? date('Y-m-d H:i:s', $item['add_time']) : '';
                $item['download_url'] = get_static_url($item['filepath']);
                $item['state'] = isset($state[$item['state']]) ? $state[$item['state']] : '';

                $item['edit_action'] = $this->url->link('system/version/edit', 'version_id='.$item['version_id']);
                $item['delete_action'] = $this->url->link('system/version/delete', 'version_id='.$item['version_id']);
                $item['info_action'] = $this->url->link('system/version/info', 'version_id='.$item['version_id']);
            }
        }

        $data_columns = $this->getDataColumns();
        $this->assign('data_columns', $data_columns);
        $this->assign('data_rows', $result);
        $this->assign('action', $this->cur_url);
        $this->assign('add_action', $this->url->link('system/version/add'));

        if (isset($this->session->data['success'])) {
            $this->assign('success', $this->session->data['success']);
            unset($this->session->data['success']);
        }

        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $page;
        $pagination->page_size = $rows;
        $pagination->url = $this->cur_url . '&amp;page={page}';
        $pagination = $pagination->render();
        $results = sprintf($this->language->get('text_pagination'), ($total) ? $offset + 1 : 0, ($offset > ($total - $rows)) ? $total : ($offset + $rows), $total, ceil($total / $rows));

        $this->assign('pagination', $pagination);
        $this->assign('results', $results);

        $this->response->setOutput($this->load->view('system/version_list', $this->output));
    }

    /**
     * 表格字段
     * @return mixed
     */
    protected function getDataColumns() {
        $this->setDataColumn('版本');
        $this->setDataColumn('更新内容');
        $this->setDataColumn('下载地址');
        $this->setDataColumn('更新时间');
        $this->setDataColumn('状态');
        return $this->data_columns;
    }

    /**
     * 添加版本
     */
    public function add() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $input = $this->request->post(array('filepath', 'version_code', 'version_name', 'description', 'state'));
            $now = time();
            $data = array(
                'filepath' => $input['filepath'],
                'version_code' => $input['version_code'],
                'version_name' => $input['version_name'],
                'description' => $input['description'],
                'state' => $input['state'],
                'add_time' => $now
            );
            $this->sys_model_version->addVersion($data);

            $this->session->data['success'] = '添加版本成功！';

            $this->load->controller('common/base/redirect', $this->url->link('system/version', '', true));
        }

        $this->assign('title', '添加新版本');
        $this->getForm();
    }

    /**
     * 编辑版本
     */
    public function edit() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $input = $this->request->post(array('filepath', 'version_code', 'version_name', 'description', 'state'));
            $version_id = $this->request->get['version_id'];
            $data = array(
                'filepath' => $input['filepath'],
                'version_code' => $input['version_code'],
                'version_name' => $input['version_name'],
                'description' => $input['description'],
                'state' => $input['state']
            );
            $condition = array(
                'version_id' => $version_id
            );
            $this->sys_model_version->updateVersion($condition, $data);

            $this->session->data['success'] = '编辑版本成功！';


            $this->load->controller('common/base/redirect', $this->url->link('system/version', '', true));
        }

        $this->assign('title', '编辑版本');
        $this->getForm();
    }


    private function getForm() {
        // 编辑时获取已有的数据
        $info = $this->request->post(array('filepath', 'version_code', 'version_name', 'description', 'state'));
        $version_id = $this->request->get('version_id');
        if (isset($this->request->get['version_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $condition = array(
                'version_id' => $this->request->get['version_id']
            );
            $info = $this->sys_model_version->getVersionInfo($condition);
        }

        $this->assign('data', $info);
        $this->assign('action', $this->cur_url . '&version_id=' . $version_id);
        $this->assign('upload_url', $this->url->link('common/upload'));
        $this->assign('error', $this->error);

        $this->response->setOutput($this->load->view('system/version_form', $this->output));
    }

    /**
     * 验证表单数据
     * @return bool
     */
    private function validateForm() {
        $info = $this->request->post(array('filepath', 'version_code', 'version_name', 'description', 'state'));
        if (empty($info['filepath']) || empty($info['version_code']) || empty($info['version_name'])) {
            $this->error['filepath'] = '请上传文件！';
        }
        if (empty($info['description'])) {
            $this->error['description'] = '请完善此项！';
        }

        if ($this->error) {
            $this->error['warning'] = '警告: 存在错误，请检查！';
        }
        return !$this->error;
    }

}