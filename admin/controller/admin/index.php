<?php
class ControllerAdminIndex extends Controller {
    /**
     * 首页
     */
    public function index() {
        $this->summary();
        $this->assign('servertime', time());
        $this->assign('export_action', $this->url->link('bicycle/bicycle/export'));
        $this->response->setOutput($this->load->view('admin/index', $this->output));
    }

    public function apiGetMarker() {
        $marker_init = isset($this->request->post['marker_init']) ? $this->request->post['marker_init'] : 0;
        $min_lat = $marker_init ? -90 : $this->request->post['min_lat'];
        $min_lng = $marker_init ? -180 : $this->request->post['min_lng'];
        $max_lat = $marker_init ? 90 : $this->request->post['max_lat'];
        $max_lng = $marker_init ? 180 : $this->request->post['max_lng'];

        $status = isset($this->request->post['status']) ? $this->request->post['status'] : false;

        $this->load->library('sys_model/bicycle', true);
        $markers = $this->sys_model_bicycle->getBicyclesByBounds($min_lat, $min_lng, $max_lat, $max_lng, $status);
        $this->response->showSuccessResult($markers);
    }

    public function apiGetFaults() {
        $page = isset($this->request->request['page']) ? $this->request->request['page'] : 1;
        $this->load->library('sys_model/fault',true);

        $bike_sn = $this->request->request['bike_sn'];

        $order = 'add_time DESC';
        $rows = $this->config->get('config_limit_admin');
        $offset = ($page - 1) * $rows;
        $limit = sprintf('%d, %d', $offset, $rows);

        $faults = $this->sys_model_fault->getFaultList(array('bicycle_sn'=>$bike_sn),$order, $limit);
        $get_fault_processed = get_fault_processed();

        $condition = array(
            'is_show' => 1
        );
        $order = 'display_order ASC, add_time DESC';
        $tempFaultTypes = $this->sys_model_fault->getFaultTypeList($condition, $order);
        $fault_types = array();
        if (!empty($tempFaultTypes)) {
            foreach ($tempFaultTypes as $v) {
                $fault_types[$v['fault_type_id']] = $v['fault_type_name'];
            }
        }

        foreach ($faults as &$v){
            $v['processed'] = $get_fault_processed[$v['processed']];
            $v['add_time'] = date('Y-m-d H:m:s',$v['add_time']);

            $fault_type = '';
            $fault_type_ids = explode(',', $v['fault_type']);
            foreach($fault_type_ids as $fault_type_id) {
                $fault_type .= isset($fault_types[$fault_type_id]) ? ',' . $fault_types[$fault_type_id] : '';
            }
            $v['fault_type'] = !empty($fault_type) ? substr($fault_type, 1) : '';
        }

        $this->assign('page', $page+1);
        $this->assign('faults', $faults);
        $this->assign('static', HTTP_IMAGE);
        $this->assign('config_limit_admin', $rows);

        $this->response->setOutput($this->load->view('admin/fault', $this->output));
    }

    public function apiGetNormalParking() {
        $page = isset($this->request->request['page']) ? $this->request->request['page'] : 1;
        $this->load->library('sys_model/fault',true);

        $bike_sn = $this->request->request['bike_sn'];

        $order = 'add_time DESC';
        $rows = $this->config->get('config_limit_admin');
        $offset = ($page - 1) * $rows;
        $limit = sprintf('%d, %d', $offset, $rows);

        $normalParking = $this->sys_model_fault->getNormalParkingList(array('bicycle_sn'=>$bike_sn), $order, $limit);

        foreach ($normalParking as &$v){
            $v['add_time'] = date('Y-m-d H:m:s',$v['add_time']);
        }

        $this->assign('page', $page+1);
        $this->assign('parkings', $normalParking);
        $this->assign('config_limit_admin', $rows);
        $this->assign('static', HTTP_IMAGE);

        $this->response->setOutput($this->load->view('admin/normal_parking', $this->output));

    }

    public function apiGetIllegalParking() {
        $page = isset($this->request->request['page']) ? $this->request->request['page'] : 1;
        $this->load->library('sys_model/fault',true);

        $bike_sn = $this->request->request['bike_sn'];

        $order = 'add_time DESC';
        $rows = $this->config->get('config_limit_admin');
        $offset = ($page - 1) * $rows;
        $limit = sprintf('%d, %d', $offset, $rows);

        $illegalParking = $this->sys_model_fault->getIllegalParkingList(array('bicycle_sn'=>$bike_sn), $order, $limit);
        $type = array(
          '1' => '违停上报',
          '2' => '其他上报',
        );

        foreach ($illegalParking as &$v){
            $v['add_time'] = date('Y-m-d H:m:s',$v['add_time']);
            $v['type'] = $type[$v['type']];
        }

        $this->assign('page', $page+1);
        $this->assign('parkings', $illegalParking);
        $this->assign('config_limit_admin', $rows);
        $this->assign('static', HTTP_IMAGE);

        $this->response->setOutput($this->load->view('admin/illegal_parking', $this->output));

    }

    public function apiGetFeekbacks() {
        $page = isset($this->request->request['page']) ? $this->request->request['page'] : 1;

        $data = array(
            'feedbacks' => array()
        );

        $this->response->setOutput($this->load->view('admin/feedback', $data));
    }

    public function apiGetUsedHistory() {
        $page = isset($this->request->request['page']) ? $this->request->request['page'] : 1;
        $this->load->library('sys_model/orders',true);

        $bike_sn = $this->request->request['bike_sn'];

        $order = 'add_time DESC';
        $rows = $this->config->get('config_limit_admin');
        $offset = ($page - 1) * $rows;
        $limit = sprintf('%d, %d', $offset, $rows);

        $get_order_state = get_order_state();

        $orders = $this->sys_model_orders->getOrdersList(array('bicycle_sn'=>$bike_sn), $order, $limit);
        foreach ($orders as &$v){
            $v['add_time'] = date('Y-m-d H:m:s',$v['add_time']);
            $v['order_state_describe'] = $get_order_state[$v['order_state']];
        }

        $this->assign('page', $page+1);
        $this->assign('records', $orders);
        $this->assign('config_limit_admin', $rows);
        $this->assign('static', HTTP_IMAGE);

        $this->response->setOutput($this->load->view('admin/used_history', $this->output));
    }

    //关锁
    function shut(){
        $device_id = $this->request->request['device_id'];
        $this->load->library('instructions/instructions',true);
        $this->response->showSuccessResult($this->instructions_instructions->closeLock($device_id));
    }

    //开锁
    function openLock(){
        $device_id = $this->request->request['device_id'];
        $this->load->library('instructions/instructions',true);
        $this->response->showSuccessResult($this->instructions_instructions->openLock($device_id));
    }

    //设置设备锁关时位置回传间隔
    function setGapTime2(){
        $time = $this->request->request['time'];
        $device_id = $this->request->request['device_id'];
        $this->load->library('instructions/instructions',true);
        $this->load->library('sys_model/lock',true);
        if($this->sys_model_lock->updateLock(array('lock_sn' =>$device_id), array('set_gap_time2'=> $time))){
            $this->response->showSuccessResult($this->instructions_instructions->setGapTime2($device_id, $time));
        };
    }

    //设置设备锁开是位置回传间隔
    function setGapTime(){
        $time = $this->request->request['time'];
        $device_id = $this->request->request['device_id'];
        $this->load->library('instructions/instructions',true);
        $this->load->library('sys_model/lock',true);
        if($this->sys_model_lock->updateLock(array('lock_sn' =>$device_id), array('set_gap_time'=> $time))){
            $this->response->showSuccessResult($this->instructions_instructions->setGapTime($device_id, $time));
        }else{

        };
    }

    //响铃
    function beepLock(){
        $device_id = $this->request->request['device_id'];
        $this->load->library('instructions/instructions',true);
        $this->response->showSuccessResult($this->instructions_instructions->beepLock($device_id));
    }

    //锁资料
    function lockInfo(){
        $device_id = $this->request->request['device_id'];
        $this->load->library('sys_model/lock',true);
        $this->response->showSuccessResult($this->sys_model_lock->getLockInfo(array('lock_sn'=> $device_id)));
    }

    //首页地图右侧搜索单车
    function search(){
        $filter = $this->request->post(array('bicycle_sn','fault','illegal_parking','low_battery'));

        $condition = array();
        if (!empty($filter['bicycle_sn'])) {
            $condition['bicycle_sn'] = array('like', "%{$filter['bicycle_sn']}%");
        }
        if (!empty($filter['fault'])) {
            $condition['fault'] = $filter['fault'];
        }
        if (!empty($filter['illegal_parking'])) {
            $condition['illegal_parking'] = $filter['illegal_parking'];
        }
        if (!empty($filter['low_battery'])) {
            $condition['low_battery'] = $filter['low_battery'];
        }

        $field = 'bicycle_sn, bicycle_id';
        $order = 'add_time DESC';
        $this->load->library('sys_model/bicycle',true);
        $result = $this->sys_model_bicycle->getBicycleList($condition, $order, '',  $field);
        $this->response->showSuccessResult($result);
    }

    //合伙人列表
    function cooperator(){
        $this->load->library('sys_model/cooperator',true);
        $this->load->library('sys_model/region',true);
        $cooperator = $this->sys_model_cooperator->getCooperatorList('', 'cooperator_id ASC', '',  '');
        $region = $this->sys_model_region->getRegionList('', 'region_id ASC', '',  '');
        $cooperatorToRegion = $this->sys_model_region->getCooperatorToRegionList('');

        $this->response->showSuccessResult(array(
            'cooperator'=> $cooperator,
            'region'=> $region,
            'cooperatorToRegion'=> $cooperatorToRegion,
        ));
    }

    private function summary() {
        $this->load->library('sys_model/data_sum',true);
        $user_sum = $this->sys_model_data_sum->getRegisterSum();
        $bicycle_sum = $this->sys_model_data_sum->getBicycleSum();
        $used_sum = $this->sys_model_data_sum->getUsedBicycleSum();
        $fault_sum = $this->sys_model_data_sum->getFaultBicycleSum();
        $recharge_sum = $this->sys_model_data_sum->getRechargeSum();
        $deposit_sum = $this->sys_model_data_sum->getDepositSum();
        $coupon_sum = $this->sys_model_data_sum->getCouponSum();
        $this->assign('user_sum', $user_sum);
        $this->assign('bicycle_sum', $bicycle_sum);
        $this->assign('used_sum', $used_sum);
        $this->assign('fault_sum', $fault_sum);
        $this->assign('recharge_sum', $recharge_sum);
        $this->assign('deposit_sum', $deposit_sum);
        $this->assign('coupon_sum', $coupon_sum);
    }
}