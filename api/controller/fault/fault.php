<?php
class ControllerFaultFault extends Controller {
    /**
     * 获取故障类型（弃用）
     */
    public function getFaultType() {
        $this->load->library('sys_model/fault');
        $result = $this->sys_model_fault->getAllFaultType();
        $this->response->showSuccessResult($result, '数据获取成功');
    }

    /**
     * 上报故障
     */
    public function addFault() {
        if (!isset($this->request->post['bicycle_sn']) || !isset($this->request->post['fault_type'])) {
            $this->response->showErrorResult('参数错误或缺失',1);
        }

        $user_info = $this->startup_user->getUserInfo();
        $data['user_id'] = $this->startup_user->userId();
        $data['user_name'] = $user_info['mobile'];
        $data['bicycle_sn'] = $this->request->post['bicycle_sn'];
        if(strlen($data['bicycle_sn'])==11) {
            $data['bicycle_sn'] = substr($data['bicycle_sn'], 5);
        }

        $data['fault_type'] = (is_array($this->request->post['fault_type']) && !empty($this->request->post['fault_type'])) ? implode(',', $this->request->post['fault_type']) : $this->request->post['fault_type'];
        $data['add_time'] = time();
        $data['lat'] = $this->request->post['lat'];
        $data['lng'] = $this->request->post['lng'];

        if (empty($data['bicycle_sn'])) {
            $this->response->showErrorResult('单车编号不能为空', 138);
        }

        if (empty($data['lat'])) {
            $this->response->showErrorResult('纬度不能为空', 136);
        }

        if (empty($data['lng'])) {
            $this->response->showErrorResult('经度不能为空', 137);
        }

        $data['fault_content'] = isset($this->request->post['fault_content']) ? $this->request->post['fault_content'] : '';

        $this->load->library('sys_model/bicycle', true);
        $bicycle_info = $this->sys_model_bicycle->getBicycleInfo(array('bicycle_sn' => $data['bicycle_sn']));
        if (empty($bicycle_info)) {
            $this->response->showErrorResult('系统不存在此编号的单车' . $data['bicycle_sn'], 140);
        }
        //添加单车编号
        $data['lock_sn'] = $bicycle_info['lock_sn'];

        $this->load->library('sys_model/fault', true);
        //h5可能会用到base64记得转码的问题，获取到的数据需要base64_decode
        $file_info['state'] = 'FAILURE';
        if (isset($this->request->files['fault_image']) || isset($this->request->post['fault_image'])) {
            $uploader = new Uploader(
                'fault_image',
                array(
                    'allowFiles' => array('.jpeg', '.jpg', '.png'),
                    'maxSize' => 10 * 1024 * 1024,
                    'pathFormat' => 'fault/{yyyy}{mm}{dd}{hh}{ii}{ss}{rand:4}'
                ),
                empty($this->request->files['fault_image']) ? 'base64' : 'upload', // upload, base64 or remote
                $this->request->files //文件上传变量数组，base64的不用提供，内部直接用$_POST[字段名]作为数据
            );
            $file_info = $uploader->getFileInfo();
        }

        if ($file_info['state'] == 'SUCCESS') {
            $data['fault_image'] = $file_info['url'];
        }

        $insert_id = $this->sys_model_fault->addFault($data);

        //更新bicycle表的fault字段
        $this->sys_model_bicycle->updateBicycle(array('bicycle_sn' => $data['bicycle_sn']), array('fault'=>1));

        $insert_id ? $this->response->showSuccessResult(array('fault_id' => $insert_id), '上报成功') : $this->response->showErrorResult('数据库操作失败', 4);
    }

    /**
     * 违规停车
     */
    public function addIllegalParking() {
        if (!isset($this->request->post['lat']) || empty($this->request->post['lat'])) {
            $this->response->showErrorResult('纬度不能为空', 136);
        }
        if (!isset($this->request->post['lng']) || empty($this->request->post['lng'])) {
            $this->response->showErrorResult('经度不能为空', 137);
        }
        if (!isset($this->request->post['type']) || empty($this->request->post['type'])) {
            $this->request->post['type'] = 1;
        }
        if (!isset($this->request->post['bicycle_sn']) || empty($this->request->post['bicycle_sn'])) {
            $this->response->showErrorResult('单车编号不能为空', 138);
        }

        $user_info = $this->startup_user->getUserInfo();
        $data['bicycle_sn'] = $this->request->post['bicycle_sn'];
        if(strlen($data['bicycle_sn'])==11) {
            $data['bicycle_sn'] = substr($data['bicycle_sn'], 5);
        }


        $data['lat'] = $this->request->post['lat'];
        $data['lng'] = $this->request->post['lng'];
        $data['content'] = isset($this->request->post['content']) ? $this->request->post['content'] : '';
        $data['user_id'] = $user_info['user_id'];
        $data['user_name'] = $user_info['mobile'];
        $data['type'] = $this->request->post['type'];
        $data['add_time'] = time();
        $this->load->library('sys_model/bicycle', true);
        $bicycle_info = $this->sys_model_bicycle->getBicycleInfo(array('bicycle_sn' => $data['bicycle_sn']));
        if (empty($bicycle_info)) {
            $this->response->showErrorResult('系统不存在此编号的单车', 140);
        }
        $file_info['state'] = 'FAILURE';
        if (isset($this->request->files['file_image']) || isset($this->request->post['file_image'])) {
            $uploader = new Uploader(
                'file_image',
                array(
                    'allowFiles' => array('.jpeg', '.jpg', '.png'),
                    'maxSize' => 10 * 1024 * 1024,
                    'pathFormat' => 'illegal_parking/{yyyy}{mm}{dd}{hh}{ii}{ss}{rand:4}'
                ),
                empty($this->request->files['file_image']) ? 'base64' : 'upload', // upload, base64 or remote
                $this->request->files //文件上传变量数组，base64的不用提供，内部直接用$_POST[字段名]作为数据
            );
            $file_info = $uploader->getFileInfo();
        }

        if ($file_info['state'] == 'SUCCESS') {
            $data['file_image'] = $file_info['url'];
        }
        $this->load->library('sys_model/fault', true);
        $insert_id = $this->sys_model_fault->addIllegalParking($data);

        //更新bicycle表的illegal_parking字段
        $this->sys_model_bicycle->updateBicycle(array('bicycle_sn' => $data['bicycle_sn']), array('illegal_parking'=>1));

        $insert_id ? $this->response->showSuccessResult(array('parking_id' => $insert_id), '上报成功') : $this->response->showErrorResult('数据库操作失败',4);
    }
}