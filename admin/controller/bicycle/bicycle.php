<?php
class ControllerBicycleBicycle extends Controller {
    private $cur_url = null;
    private $error = null;
    
    public function __construct($registry) {
        parent::__construct($registry);
        $this->language->load('bicycle/bicycle');
        $languages = $this->language->all();
        $this->assign('languages',$languages);
        // 当前网址
        $this->cur_url = isset($this->request->get['route']) ? $this->url->link($this->request->get['route']) : '';

        // 加载bicycle Model
        $this->load->library('sys_model/bicycle', true);
        $this->load->library('sys_model/lock', true);

        // 加载 region Model
        $this->load->library('sys_model/region', true);
    }

    /**
     * 单车列表
     */
    public function index() {
        $filter = $this->request->get(array('bicycle_sn', 'type', 'lock_sn', 'region_name', 'cooperator_name', 'is_using'));

        $condition = array();
        if (!empty($filter['bicycle_sn'])) {
            $condition['bicycle_sn'] = array('like', "%{$filter['bicycle_sn']}%");
        }
        if (is_numeric($filter['type'])) {
            $condition['type'] = (int)$filter['type'];
        }
        if (!empty($filter['lock_sn'])) {
            $condition['lock_sn'] = array('like', "%{$filter['lock_sn']}%");
        }
        if (!empty($filter['region_name'])) {
            $condition['region.region_name'] = array('like', "%{$filter['region_name']}%");
        }
        if (!empty($filter['cooperator_name'])) {
            $condition['cooperator.cooperator_name'] = array('like', "%{$filter['cooperator_name']}%");
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

        $field = 'bicycle.*,region.region_name,cooperator.cooperator_name';

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

                $item['edit_action'] = $this->url->link('bicycle/bicycle/edit', 'bicycle_id='.$item['bicycle_id']);
                $item['delete_action'] = $this->url->link('bicycle/bicycle/delete', 'bicycle_id='.$item['bicycle_id']);
                $item['info_action'] = $this->url->link('bicycle/bicycle/info', 'bicycle_id='.$item['bicycle_id']);
            }
        }

        $filter_types = array(
            'bicycle_sn' => $this->language->get('dcbh'),
            'lock_sn' => $this->language->get('csbh'),
            //'region_name' => $this->language->get('quyu'),
            // 'cooperator_name' => $this->language->get('hhr'),
        );
        $filter_type = $this->request->get('filter_type');
        if (empty($filter_type)) {
            reset($filter_types);
            $filter_type = key($filter_types);
        }

        // 使用中单车数
        $condition = array(
            'is_using' => 2
        );
        $using_bicycle = $this->sys_model_bicycle->getTotalBicycles($condition);
        // 故障单车数
        $condition = array(
            'is_using' => 1
        );
        $fault_bicycle = $this->sys_model_bicycle->getTotalBicycles($condition);

        $data_columns = $this->getDataColumns();
        $this->assign('data_columns', $data_columns);
        $this->assign('data_rows', $result);
        $this->assign('total_bicycle', $total);
        $this->assign('using_bicycle', $using_bicycle);
        $this->assign('fault_bicycle', $fault_bicycle);
        $this->assign('model', $model);
        $this->assign('filter', $filter);
        $this->assign('filter_type', $filter_type);
        $this->assign('filter_types', $filter_types);
        $this->assign('action', $this->cur_url);
        $this->assign('import_action', $this->url->link('bicycle/bicycle/import'));
        $this->assign('add_action', $this->url->link('bicycle/bicycle/add'));
        $this->assign('batchadd_action', $this->url->link('bicycle/bicycle/batchadd'));
        $this->assign('lock_action', $this->url->link('lock/lock'));
        $this->assign('export_action', $this->url->link('bicycle/bicycle/export'));
        $this->assign('export_qrcode_action', $this->url->link('bicycle/bicycle/export_qrcode'));

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
        $this->setDataColumn($this->language->get('dcbh'));
        $this->setDataColumn($this->language->get('csbh'));
        $this->setDataColumn($this->language->get('dclx'));
        // $this->setDataColumn($this->language->get('chengshi'));
        $this->setDataColumn($this->language->get('sfsyz'));
        // $this->setDataColumn('城市');
        // $this->setDataColumn('合伙人');
        // $this->setDataColumn('是否使用中');
        return $this->data_columns;
    }

    /**
     * 批量添加单车
     */
    public function batchadd() {
        $bicycle_ids = $bicycles = array();
        $bicycle_types = get_bicycle_type();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateBatchaddForm()) {
            $input = $this->request->post(array('bicycle_sn_start', 'bicycle_sn_end', 'type', 'lock_sn', 'region_id'));

            // 区域信息
            $condition = array(
                'region_id' => $input['region_id']
            );
            $region = $this->sys_model_region->getRegionInfo($condition);
            $now = time();

            $data = array(
                'region_id' => $input['region_id'],
                'region_name' => $region['region_name'],
                'type' => (int)$input['type'],
                'add_time' => $now
            );
            $bicycleData = array(
                'region_id' => $input['region_id'],
                'region_name' => $region['region_name'],
                'type' => (int)$input['type'],
                'type_name' => $bicycle_types[$data['type']],
                'add_time' => $now
            );

            $bicycle_num = $input['bicycle_sn_end'] - $input['bicycle_sn_start'];

            for ($i = 0; $i <= $bicycle_num; $i++) {
                $bicycle_sn = sprintf('%06d', $input['bicycle_sn_start'] + $i);
                $rec = $this->checkBicycleSN($bicycle_sn);
                if (!$rec) {
                    continue;
                }
                $bicycleData['bicycle_sn'] = $data['bicycle_sn'] = $bicycle_sn;
                $bicycle_id = $this->sys_model_bicycle->addBicycle($data);
                $bicycleData['bicycle_id'] = $bicycle_ids[] = $bicycle_id;
                $bicycles[] = $bicycleData;

                // 生成二维码图片
                $qrcodeInfo = array(
                    'qrcodeText' => sprintf('http://121.42.254.23/app.php?b=%03d%02d%06d', $region['region_city_code'], $region['region_city_ranking'], $data['bicycle_sn']),
                    'fullcode' => sprintf('%03d%02d %06d', $region['region_city_code'], $region['region_city_ranking'], $data['bicycle_sn']),
                    'code' => $data['bicycle_sn']
                );
                $this->load->controller('common/qrcode/buildQrCode', $qrcodeInfo);
                $this->load->controller('common/qrcode/buildWordImage', $qrcodeInfo);
                $this->load->controller('common/qrcode/buildFrontQrCode', $qrcodeInfo);
                $this->load->controller('common/qrcode/buildBackQrCode', $qrcodeInfo);
            }

            //加载管理员操作日志 model
            $this->load->library('sys_model/admin_log', true);
            $data = array(
                'admin_id' => $this->logic_admin->getId(),
                'admin_name' => $this->logic_admin->getadmin_name(),
                'log_description' => '批量添加单车：' . implode(',', $bicycle_ids),
                'log_ip' => $this->request->ip_address(),
                'log_time' => date('Y-m-d H:i:s')
            );
            $this->sys_model_admin_log->addAdminLog($data);

            $this->session->data['success'] = '批量添加单车成功！';
        }

        $this->assign('title', '批量添加');

        // 编辑时获取已有的数据
        $info = $this->request->post(array('bicycle_num', 'type', 'region_id'));

        $condition = array();
        $order = 'region_sort ASC';
        $regionList = $this->sys_model_region->getRegionList($condition, $order);

        $this->assign('data', $info);
        $this->assign('bicycles', $bicycles);
        $this->assign('regions', $regionList);
        $this->assign('types', get_bicycle_type());
        $this->assign('action', $this->cur_url);
        $this->assign('return_action', $this->url->link('bicycle/bicycle'));
        $this->assign('export_qrcode_action', $this->url->link('bicycle/bicycle/export_qrcode'));
        $this->assign('error', $this->error);

        $this->response->setOutput($this->load->view('bicycle/bicycle_batchadd', $this->output));
    }

    /**
     * 添加单车
     */
    public function add() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $input = $this->request->post(array('bicycle_sn', 'type', 'lock_sn', 'region_id'));

            // 区域信息
            $condition = array(
                'region_id' => $input['region_id']
            );
            $region = $this->sys_model_region->getRegionInfo($condition);

            $now = time();
            $data = array(
                'bicycle_sn' => $input['bicycle_sn'],
                'region_id' => $input['region_id'],
                'region_name' => $region['region_name'],
                'type' => (int)$input['type'],
                'lock_sn' => $input['lock_sn'],
                'add_time' => $now
            );
            $bicycle_id = $this->sys_model_bicycle->addBicycle($data);

            //加载管理员操作日志 model
            $this->load->library('sys_model/admin_log', true);
            $data = array(
                'admin_id' => $this->logic_admin->getId(),
                'admin_name' => $this->logic_admin->getadmin_name(),
                'log_description' => '添加单车：' . $bicycle_id,
                'log_ip' => $this->request->ip_address(),
                'log_time' => date('Y-m-d H:i:s')
            );
            $this->sys_model_admin_log->addAdminLog($data);


            // 生成二维码图片
            $data = array(
                'qrcodeText' => sprintf('http://121.42.254.23/app.php?b=%03d%02d%06d', $region['region_city_code'], $region['region_city_ranking'], $input['bicycle_sn']),
                'fullcode' => sprintf('%03d%02d %06d', $region['region_city_code'], $region['region_city_ranking'], $input['bicycle_sn']),
                'code' => $input['bicycle_sn']
            );
            $this->load->controller('common/qrcode/buildQrCode', $data);
            $this->load->controller('common/qrcode/buildWordImage', $data);
            $this->load->controller('common/qrcode/buildFrontQrCode', $data);
            $this->load->controller('common/qrcode/buildBackQrCode', $data);


            $this->session->data['success'] = $this->language->get('tjdccg');
            
            $filter = array('bicycle_sn', 'type', 'lock_sn', 'region_name', 'cooperator_name', 'is_using');

            $this->load->controller('common/base/redirect', $this->url->link('bicycle/bicycle', $filter, true));
        }

        $this->assign('title', $this->language->get('xzdc'));
        $this->getForm();
    }

    /**
     * 编辑单车
     */
    public function edit() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $input = $this->request->post(array('bicycle_sn', 'type', 'lock_sn', 'region_id'));
            $bicycle_id = $this->request->get['bicycle_id'];

            // 区域信息
            $condition = array(
                'region_id' => $input['region_id']
            );
            $region = $this->sys_model_region->getRegionInfo($condition);

            $data = array(
                'bicycle_sn' => $input['bicycle_sn'],
                'region_id' => $input['region_id'],
                'region_name' => $region['region_name'],
                'type' => (int)$input['type'],
                'lock_sn' => $input['lock_sn']
            );
            $condition = array(
                'bicycle_id' => $bicycle_id
            );
            $this->sys_model_bicycle->updateBicycle($condition, $data);

            //加载管理员操作日志 model
            $this->load->library('sys_model/admin_log', true);
            $data = array(
                'admin_id' => $this->logic_admin->getId(),
                'admin_name' => $this->logic_admin->getadmin_name(),
                'log_description' => '编辑单车：' . $bicycle_id,
                'log_ip' => $this->request->ip_address(),
                'log_time' => date('Y-m-d H:i:s')
            );
            $this->sys_model_admin_log->addAdminLog($data);

            // 生成二维码图片
            $data = array(
                'qrcodeText' => sprintf('http://121.42.254.23/app.php?b=%03d%02d%06d', $region['region_city_code'], $region['region_city_ranking'], $input['bicycle_sn']),
                'fullcode' => sprintf('%03d%02d %06d', $region['region_city_code'], $region['region_city_ranking'], $input['bicycle_sn']),
                'code' => $input['bicycle_sn']
            );
            $this->load->controller('common/qrcode/buildQrCode', $data);
            $this->load->controller('common/qrcode/buildWordImage', $data);
            $this->load->controller('common/qrcode/buildFrontQrCode', $data);
            $this->load->controller('common/qrcode/buildBackQrCode', $data);

            $this->session->data['success'] = '编辑单车成功！';

            $filter = array('bicycle_sn', 'type', 'lock_sn', 'region_name', 'cooperator_name', 'is_using');

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

            //加载管理员操作日志 model
            $this->load->library('sys_model/admin_log', true);
            $data = array(
                'admin_id' => $this->logic_admin->getId(),
                'admin_name' => $this->logic_admin->getadmin_name(),
                'log_description' => $this->language->get('scdc'). $this->request->get['bicycle_id'],
                'log_ip' => $this->request->ip_address(),
                'log_time' => date('Y-m-d H:i:s')
            );
            $this->sys_model_admin_log->addAdminLog($data);

            $this->session->data['success'] = $this->language->get('scdccg');
        }
        $filter = array('bicycle_sn', 'type', 'lock_sn', 'region_name', 'cooperator_name', 'is_using');
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
        $this->assign('return_action', $this->url->link('bicycle/bicycle'));

        $this->response->setOutput($this->load->view('bicycle/bicycle_info', $this->output));
    }

    /**
     * 导入单车
     */
    public function import() {
        // 获取上传EXCEL文件数据
        $excelData = $this->load->controller('common/base/importExcel');

        if (is_array($excelData) && !empty($excelData)) {
            $count = count($excelData);
            // 从第3行开始
            if ($count >= 3) {
                for ($i = 3; $i <= $count; $i++) {
                    $data = array(
                        'bicycle_sn' => isset($excelData[$i][0]) ? $excelData[$i][0] : '',
                        'type' => 1,
                        'lock_sn' => isset($excelData[$i][1]) ? $excelData[$i][1] : '',
                        'add_time' => TIMESTAMP
                    );
                    $this->sys_model_bicycle->addBicycle($data);
                }
            }
        }

        $this->response->showSuccessResult('', '导入成功');
    }

    /**
     * 导出
     */
    public function export() {
        $filter = $this->request->post(array('bicycle_sn', 'type', 'lock_sn', 'region_name', 'cooperator_name', 'is_using'));

        $condition = array();
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
            $condition['region_name'] = array('like', "%{$filter['region_name']}%");
        }
        if (!empty($filter['cooperator_name'])) {
            $condition['cooperator.cooperator_name'] = array('like', "%{$filter['cooperator_name']}%");
        }
        if (is_numeric($filter['is_using'])) {
            $condition['is_using'] = (int)$filter['is_using'];
        }
        $order = 'bicycle.add_time DESC';
        $limit = '';
        $field = 'bicycle.*,cooperator.cooperator_name';

        $join = array(
            'cooperator' => 'cooperator.cooperator_id=bicycle.cooperator_id'
        );
        $bicycles = $this->sys_model_bicycle->getBicycleList($condition, $order, $limit, $field, $join);
        $list = array();
        if (is_array($bicycles) && !empty($bicycles)) {
            $bicycle_types = get_bicycle_type();
            $use_states = get_common_boolean();
            foreach ($bicycles as $bicycle) {
                $list[] = array(
                    'bicycle_sn' => $bicycle['bicycle_sn'],
                    'lock_sn' => $bicycle['lock_sn'],
                    'type' => $bicycle_types[$bicycle['type']],
                    // 'region_name' => $bicycle['region_name'],
                    'is_using' => $use_states[$bicycle['is_using']]
                );
            }
        }

        $data = array(
            'title' => $this->language->get('dclb'),
            'header' => array(
                'bicycle_sn' => $this->language->get('dcbh'),
                'lock_sn' => $this->language->get('csbh'),
                'type' => $this->language->get('dclx'),
                // 'region_name' => $this->language->get('quyu'),
                'is_using' => $this->language->get('sfsyz'),
            ),
            'list' => $list
        );
        $this->load->controller('common/base/exportExcel', $data);
    }

    /**
     * 导出二维码
     */
    public function export_qrcode() {
        if (isset($this->request->get['operation']) && $this->request->get['operation'] == 'export') {
            $filter = $this->request->post(array('bicycle_sn', 'type', 'lock_sn', 'region_name', 'cooperator_name', 'is_using', 'add_time'));

            $condition = array();
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
            if (!empty($filter['cooperator_name'])) {
                $condition['cooperator.cooperator_name'] = array('like', "%{$filter['cooperator_name']}%");
            }
            if (is_numeric($filter['is_using'])) {
                $condition['is_using'] = (int)$filter['is_using'];
            }
            if (!empty($filter['add_time'])) {
                $add_time = explode(' 至 ', $filter['add_time']);
                $condition['bicycle.add_time'] = array(
                    array('gt', strtotime($add_time[0])),
                    array('lt', bcadd(86399, strtotime($add_time[1])))
                );
            }
            $bicycles = $this->sys_model_bicycle->getBicycleList($condition);

            $filesname = array();
            if (!empty($bicycles) && is_array($bicycles)) {
                foreach ($bicycles as $bicycle) {
                    $filesname[] = DIR_STATIC . 'images/qrcode/' . $bicycle['bicycle_sn'] . '.png';
                    $filesname[] = DIR_STATIC . 'images/qrcode/word_' . $bicycle['bicycle_sn'] . '.png';
                    $filesname[] = DIR_STATIC . 'images/qrcode/front_' . $bicycle['bicycle_sn'] . '.png';
                    $filesname[] = DIR_STATIC . 'images/qrcode/back_' . $bicycle['bicycle_sn'] . '.png';
                }
            }

//        $filename = DIR_STATIC . 'images/qrcode/bak.zip'; //最终生成的文件名（含路径）
            $filename = tempnam("/tmp", "QRCODE");

            if (is_file($filename) && file_exists($filename)) {
                @unlink($filename);
            }
            //重新生成文件
            $zip = new ZipArchive();//使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释
            if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {
                exit('无法打开文件，或者文件创建失败');
            }
            foreach ($filesname as $val) {
                if (file_exists($val)) {
                    $zip->addFile($val, basename($val));//第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下
                }
            }
            $zip->close();//关闭
            if (!is_file($filename) || !file_exists($filename)) {
                exit("无法找到文件"); //即使创建，仍有可能失败。。。。
            }
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header('Content-disposition: attachment; filename=自行车二维码.zip'); //文件名
            header("Content-Type: application/zip"); //zip格式的
            header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
            header('Content-Length: ' . filesize($filename)); //告诉浏览器，文件大小
            @readfile($filename);
            @unlink($filename);
        } else {
            $filter = $this->request->get(array('bicycle_sn', 'type', 'lock_sn', 'region_name', 'cooperator_name', 'is_using', 'add_time'));

            $condition = array();
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
            if (!empty($filter['cooperator_name'])) {
                $condition['cooperator.cooperator_name'] = array('like', "%{$filter['cooperator_name']}%");
            }
            if (is_numeric($filter['is_using'])) {
                $condition['is_using'] = (int)$filter['is_using'];
            }
            if (!empty($filter['add_time'])) {
                $add_time = explode(' 至 ', $filter['add_time']);
                $condition['bicycle.add_time'] = array(
                    array('gt', strtotime($add_time[0])),
                    array('lt', bcadd(86399, strtotime($add_time[1])))
                );
            }

            if (isset($this->request->get['page'])) {
                $page = (int)$this->request->get['page'];
            } else {
                $page = 1;
            }

            $order = 'bicycle.add_time DESC';
            $limit = '';

            $field = 'bicycle.*,region.region_name,cooperator.cooperator_name';

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
                    $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
                    foreach ($model as $k => $v) {
                        $item[$k] = isset($v[$item[$k]]) ? $v[$item[$k]] : '';
                    }
                }
            }

            $filter_types = array(
                'bicycle_sn' => '单车编号',
                'lock_sn' => '车锁编号',
                'region_name' => '区域',
                'cooperator_name' => '合伙人',
            );
            $filter_type = $this->request->get('filter_type');
            if (empty($filter_type)) {
                reset($filter_types);
                $filter_type = key($filter_types);
            }

            // 使用中单车数
            $condition = array(
                'is_using' => 2
            );
            $using_bicycle = $this->sys_model_bicycle->getTotalBicycles($condition);
            // 故障单车数
            $condition = array(
                'is_using' => 1
            );
            $fault_bicycle = $this->sys_model_bicycle->getTotalBicycles($condition);

            $data_columns = array(
                '单车编号',
                '车锁编号',
                '单车类型',
                '区域',
                '合伙人',
                '是否使用中',
                '添加时间',
            );
            $this->assign('data_columns', $data_columns);
            $this->assign('data_rows', $result);
            $this->assign('total_bicycle', $total);
            $this->assign('using_bicycle', $using_bicycle);
            $this->assign('fault_bicycle', $fault_bicycle);
            $this->assign('model', $model);
            $this->assign('filter', $filter);
            $this->assign('filter_type', $filter_type);
            $this->assign('filter_types', $filter_types);
            $this->assign('action', $this->cur_url);
            $this->assign('import_action', $this->url->link('bicycle/bicycle/import'));
            $this->assign('add_action', $this->url->link('bicycle/bicycle/add'));
            $this->assign('batchadd_action', $this->url->link('bicycle/bicycle/batchadd'));
            $this->assign('lock_action', $this->url->link('lock/lock'));
            $this->assign('export_action', $this->url->link('bicycle/bicycle/export'));
            $this->assign('export_qrcode_action', $this->url->link('bicycle/bicycle/export_qrcode', http_build_query($filter) . '&operation=export'));

            if (isset($this->session->data['success'])) {
                $this->assign('success', $this->session->data['success']);
                unset($this->session->data['success']);
            }

            $this->response->setOutput($this->load->view('bicycle/bicycle_qrcode', $this->output));
        }
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

        // 加载区域 model
        $this->load->library('sys_model/region', true);
        $condition = array();
        $order = 'region_sort ASC';
        $regionList = $this->sys_model_region->getRegionList($condition, $order);

        $this->assign('data', $info);
        $this->assign('regions', $regionList);
        $this->assign('types', get_bicycle_type());
        $this->assign('action', $this->cur_url . '&bicycle_id=' . $bicycle_id);
        $this->assign('return_action', $this->url->link('bicycle/bicycle'));
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
                $this->error[$k] = $this->language->get('qsrwz');
            }
        }
        if ($this->error) {
            $this->error['warning'] = $this->language->get('jgczcwqjc');
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
     * 验证批量添加条件
     */
    private function validateBatchaddForm() {
        return !$this->error;
    }

    private function buildBicycleSN() {
        $bicycle_sn = token(6, 'number');

        $rec = $this->checkBicycleSN($bicycle_sn);
        if (!$rec) {
            return self::buildBicycleSN();
        }
        return $bicycle_sn;
    }

    private function checkBicycleSN($bicycle_sn) {
        $condition = array(
            'bicycle_sn' => $bicycle_sn
        );
        $rec = $this->sys_model_bicycle->getTotalBicycles($condition);
        if ($rec) {
            return false;
        }
        return true;
    }
}