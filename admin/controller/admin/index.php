<?php
class ControllerAdminIndex extends Controller {
    /**
     * 首页
     */
    public function index() {
        $this->summary();
        $this->assign('servertime', time());
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
        $data = array(
            'faults' => array(1,2)
        );

        $this->response->setOutput($this->load->view('admin/fault', $data));
    }

    public function apiGetNormalParking() {
        $page = isset($this->request->request['page']) ? $this->request->request['page'] : 1;

        $data = array(
            'parkings' => array(1,2,3)
        );

        $this->response->setOutput($this->load->view('admin/normal_parking', $data));

    }

    public function apiGetIllegalParking() {
        $page = isset($this->request->request['page']) ? $this->request->request['page'] : 1;
        $data = array(
            'parkings' => array(1,2,3,4)
        );

        $this->response->setOutput($this->load->view('admin/illegal_parking', $data));

    }

    public function apiGetFeekbacks() {
        $page = isset($this->request->request['page']) ? $this->request->request['page'] : 1;

        $data = array(
            'feedbacks' => array(1,2,3,4,5,6,7)
        );

        $this->response->setOutput($this->load->view('admin/feedback', $data));
    }

    public function apiGetUsedHistory() {
        $page = isset($this->request->request['page']) ? $this->request->request['page'] : 1;

        $data = array(
            'records' => array(1,2,3,4,5,6,7,8)
        );

        $this->response->setOutput($this->load->view('admin/used_history', $data));
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