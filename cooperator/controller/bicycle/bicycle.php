<?php
class ControllerBicycleBicycle extends Controller {
    private $cur_url = null;
    private $error = null;
    
    public function __construct($registry) {
        parent::__construct($registry);

        // 当前网址
        $this->cur_url = isset($this->request->get['route']) ? $this->url->link($this->request->get['route']) : '';

        // 加载bicycle Model
        $this->load->library('sys_model/bicycle', true);
        $this->load->library('sys_model/lock', true);
    }

    /**
     * 单车列表
     */
    public function index() {
        $filter = $this->request->get(array('bicycle_sn', 'type', 'lock_sn', 'region_name', 'is_using'));

        $condition = array(
            'cooperator.cooperator_id' => $this->logic_cooperator->getId()
        );
        if (!empty($filter['bicycle_sn'])) {
            $condition['bicycle_sn'] = array('like', "%{$filter['bicycle_sn']}%");
        }
        if (is_numeric($filter['type'])) {
            $condition['type'] = (int)$filter['type'];
        }
        if (!empty($filter['lock_sn'])) {
            $condition['lock_sn'] = $filter['lock_sn'];
        }
        if (!empty($filter['region_name'])) {
            $condition['region.region_name'] = array('like', "%{$filter['region_name']}%");
        }
        if (is_numeric($filter['is_using'])) {
            $condition['is_using'] = (int)$filter['is_using'];
        }

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        $order = 'bicycle.add_time DESC';
        $rows = $this->config->get('config_limit_admin');
        $offset = ($page - 1) * $rows;
        $limit = sprintf('%d, %d', $offset, $rows);

        $field = 'bicycle.*,region.region_name';

        $join = array(
            'region' => 'region.region_id=bicycle.region_id',
            'cooperator' => 'cooperator.cooperator_id=bicycle.cooperator_id'
        );

        $result = $this->sys_model_bicycle->getBicycleList($condition, $order, $limit, $field, $join);
        $total = $this->sys_model_bicycle->getTotalBicycles($condition, $join);

        $model = array(
            'type' => get_bicycle_type(),
            'is_using' => get_common_boolean()
        );
        if (is_array($result) && !empty($result)) {
            foreach ($result as &$item) {
                foreach ($model as $k => $v) {
                    $item[$k] = isset($v[$item[$k]]) ? $v[$item[$k]] : '';
                }

                $item['bicycle_qrcode'] = $this->url->link('common/qrcode', 'code='.$item['bicycle_sn']);

                $item['edit_action'] = $this->url->link('bicycle/bicycle/edit', 'bicycle_id='.$item['bicycle_id']);
                $item['delete_action'] = $this->url->link('bicycle/bicycle/delete', 'bicycle_id='.$item['bicycle_id']);
                $item['info_action'] = $this->url->link('bicycle/bicycle/info', 'bicycle_id='.$item['bicycle_id']);
            }
        }

        $data_columns = $this->getDataColumns();
        $this->assign('data_columns', $data_columns);
        $this->assign('data_rows', $result);
        $this->assign('model', $model);
        $this->assign('filter', $filter);
        $this->assign('action', $this->cur_url);
        $this->assign('add_action', $this->url->link('bicycle/bicycle/add'));

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

        $this->response->setOutput($this->load->view('bicycle/bicycle_list', $this->output));
    }

    /**
     * 表格字段
     * @return mixed
     */
    protected function getDataColumns() {
        $this->setDataColumn('单车编号');
        $this->setDataColumn('单车类型');
        $this->setDataColumn('车锁编号');
        $this->setDataColumn('景区');
        $this->setDataColumn('是否使用中');
        return $this->data_columns;
    }

    /**
     * 添加单车
     */
    public function add() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $input = $this->request->post(array('bicycle_sn', 'type', 'lock_sn', 'region_id'));
            $now = time();
            $data = array(
                'bicycle_sn' => $input['bicycle_sn'],
                'region_id' => $input['region_id'],
                'type' => (int)$input['type'],
                'lock_sn' => $input['lock_sn'],
                'add_time' => $now
            );
            $this->sys_model_bicycle->addBicycle($data);

            $this->session->data['success'] = '添加单车成功！';
            
            $filter = $this->request->get(array('bicycle_sn', 'type', 'lock_sn', 'is_using'));

            $this->load->controller('common/base/redirect', $this->url->link('bicycle/bicycle', $filter, true));
        }

        $this->assign('title', '单车添加');
        $this->getForm();
    }

    /**
     * 编辑单车
     */
    public function edit() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $input = $this->request->post(array('bicycle_sn', 'type', 'lock_sn', 'region_id'));
            $bicycle_id = $this->request->get['bicycle_id'];
            $data = array(
                'bicycle_sn' => $input['bicycle_sn'],
                'region_id' => $input['region_id'],
                'type' => (int)$input['type'],
                'lock_sn' => $input['lock_sn']
            );
            $condition = array(
                'bicycle_id' => $bicycle_id
            );
            $this->sys_model_bicycle->updateBicycle($condition, $data);

            $this->session->data['success'] = '编辑单车成功！';

            $filter = $this->request->get(array('bicycle_sn', 'type', 'lock_sn', 'is_using'));

            $this->load->controller('common/base/redirect', $this->url->link('bicycle/bicycle', $filter, true));
        }

        $this->assign('title', '编辑单车');
        $this->getForm();
    }

    /**
     * 删除单车
     */
    public function delete() {
        if (isset($this->request->get['bicycle_id']) && $this->validateDelete()) {
            $condition = array(
                'bicycle_id' => $this->request->get['bicycle_id']
            );
            $this->sys_model_bicycle->deleteBicycle($condition);

            $this->session->data['success'] = '删除单车成功！';
        }
        $filter = $this->request->get(array('bicycle_sn', 'type', 'lock_sn', 'is_using'));
        $this->load->controller('common/base/redirect', $this->url->link('bicycle/bicycle', $filter, true));
    }

    /**
     * 单车详情
     */
    public function info() {
        // 编辑时获取已有的数据
        $bicycle_id = $this->request->get('bicycle_id');
        $condition = array(
            'bicycle_id' => $bicycle_id
        );
        $info = $this->sys_model_bicycle->getBicycleInfo($condition);
        if (!empty($info)) {
            $model = array(
                'type' => get_bicycle_type(),
                'is_using' => get_common_boolean()
            );
            foreach ($model as $k => $v) {
                $info[$k] = isset($v[$info[$k]]) ? $v[$info[$k]] : '';
            }
            $condition = array(
                'lock_sn' => $info['lock_sn']
            );
            $lock = $this->sys_model_lock->getLockInfo($condition);
            if (!empty($lock)) {
                $info['lng'] = $lock['lng'];
                $info['lat'] = $lock['lat'];
            }
        }


        $this->assign('data', $info);

        $this->response->setOutput($this->load->view('bicycle/bicycle_info', $this->output));
    }

    private function getForm() {
        // 编辑时获取已有的数据
        $info = $this->request->post(array('bicycle_sn', 'type', 'lock_sn', 'region_id'));
        $bicycle_id = $this->request->get('bicycle_id');
        if (isset($this->request->get['bicycle_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $condition = array(
                'bicycle_id' => $this->request->get['bicycle_id']
            );
            $info = $this->sys_model_bicycle->getBicycleInfo($condition);
        }


        // 加载合伙人 model
        $this->load->library('sys_model/cooperator', true);
        $condition = array(
            'cooperator_id' => $this->logic_cooperator->getId()
        );
        $regions = $this->sys_model_cooperator->getCooperatorToRegionList($condition);
        $cooperatorRegions = array();
        if (!empty($regions) && is_array($regions)) {
            foreach ($regions as $val) {
                $cooperatorRegions[] = $val['region_id'];
            }
        }

        // 加载景区 model
        $this->load->library('sys_model/region', true);
        $condition = array(
            'region_id' => array('in', $cooperatorRegions)
        );
        $order = 'region_sort ASC';
        $regionList = $this->sys_model_region->getRegionList($condition, $order);

        $this->assign('data', $info);
        $this->assign('regions', $regionList);
        $this->assign('types', get_bicycle_type());
        $this->assign('action', $this->cur_url . '&bicycle_id=' . $bicycle_id);
        $this->assign('error', $this->error);

        $this->response->setOutput($this->load->view('bicycle/bicycle_form', $this->output));
    }

    /**
     * 验证表单数据
     * @return bool
     */
    private function validateForm() {
        $input = $this->request->post(array('bicycle_sn', 'type', 'lock_sn', 'region_id'));

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