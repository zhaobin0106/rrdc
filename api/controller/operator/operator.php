<?php
/**
 * Created by PhpStorm.
 * User: estronger
 * Date: 2016/12/9
 * Time: 16:03
 */
class ControllerOperatorOperator extends Controller {
    /**
     * 开锁
     */
    public function openLock() {
        $device_id = trim($this->request->post['device_id']);
        $cmd = $this->request->post['cmd']?trim($this->request->post['cmd']):'gprsOpenLock';

        if (empty($device_id)) {
            $this->response->showErrorResult($this->language->get('error_missing_parameter'),1);
        }

        $time = 0;
        //判断的条件写在startup
        if ($this->config->has('order_add_time')) {
            $time = $this->config->get('order_add_time');
        }
        $data = array();
        if($cmd == 'gprsOpenLock'){
            $this->instructions_instructions = new Instructions\Instructions($this->registry);
            $result = $this->instructions_instructions->openLock($device_id, $time);
            if ($result['state']) {
                $this->response->showSuccessResult($result['data']);
            }
        }else if($cmd == 'getBleMessage'){
            //蓝牙开锁，获取蓝牙信息
            $lock_info = $this->sys_model_lock->getLockInfo(array('lock_sn' => $device_id));
            $data['mac'] = $lock_info['mac'];
            $data['key'] = $lock_info['key'];
            $data['pass'] = $lock_info['pass'];
        }else{
            $this->response->showErrorResult('操作不存在', 188);
        }

        
        if ($this->order_result) {
            $data['order_sn'] = $this->order_result->order_sn;
        }

        $this->response->showSuccessResult($data, $this->language->get('success_send_open_lock_instruction'));
    }


    /**
     * 响铃
     */
    public function beepLock() {
        $device_id = $this->request->post['device_id'];
        if (empty($device_id)) {
            $this->response->showErrorResult($this->language->get('error_missing_parameter'),1);
        }
        $this->load->library('instructions/instructions', true);
        $this->instructions_instructions->beepLock($device_id);
        $this->response->showSuccessResult('', $this->language->get('success_send_beep_lock_instruction'));
    }

    /**
     * 查找锁的位置
     */
    public function selectLock() {
        $device_id = $this->request->post['device_id'];
        if (empty($device_id)) {
            $this->response->showErrorResult($this->language->get('error_missing_parameter'),1);
        }
        $this->load->library('instructions/instructions', true);
        $this->instructions_instructions->selectLocks($device_id);
        $this->response->showSuccessResult('', $this->language->get('success_send_select_lock_instruction'));
    }

    /**
     * 查找锁的位置
     */
    public function lockPosition() {
        $device_id = $this->request->post['device_id'];
        if (!$device_id) {
            $this->response->showErrorResult($this->language->get('error_missing_parameter'),1);
        }

        $this->load->library('logic/location', true);
        $result = $this->logic_location->findDeviceCurrentLocation($device_id);
        if ($result) {
            $this->response->showSuccessResult($result, $this->language->get('success_lock_position'));
        }
        $this->response->showErrorResult($this->language->get('error_lock_position'));
    }
}