<?php
/**
 * Created by PhpStorm.
 * User: estronger
 * Date: 2017/2/24
 * Time: 9:24
 */
class ControllerStartupVersion extends Controller {
    public function index() {
//        $ignore = array(
//            'system/common/version',
//            'payment/alipay/notify',
//            'payment/alipay',
//            'payment/wxpay/notify',
//            'payment/wxpay'
//        );
//        $route = strtolower($this->request->get['route']);
//        if (!isset($this->request->get['version']) && !in_array(strtolower($this->request->get['route']), $ignore)) {
//            $this->response->showErrorResult('请在url上附带上version参数');
//        }
//
//        if (!isset($this->request->get['version'])) {
//            $version = 1;
//        } else {
//            $version = $this->request->get['version'];
//        }
//
//        if ($version < VERSION_FAIL && !in_array(strtolower($this->request->get['route']), $ignore)) {
//            $this->response->showErrorResult('版本过低请升级后使用', 1024);
//        }

        //需要传锁ID的地方
        $in = array(
            'operator/operator/openlock',
            'operator/operator/beeplock',
            'account/order/book'
        );
        $route = $this->request->get['route'];
        $route = strtolower($route);
        if (in_array($route, $in)) {
            $this->load->library('sys_model/bicycle');
            $this->load->library('sys_model/region');
            $device_id = $this->request->post['device_id'];
            $region_city_code = $region_city_ranking = $bicycle_sn = '';
            if (strlen($device_id) == 11) {
                sscanf($device_id, '%03d%02d%06d', $region_city_code, $region_city_ranking, $bicycle_sn);
                $bicycle_sn = sprintf('%06d', $bicycle_sn);
            }
            $condition = array(
                'region_city_code' => $region_city_code,
                'region_city_ranking' => $region_city_ranking
            );
            $region = $this->sys_model_region->getRegionInfo($condition);

            $condition = array(
                'bicycle_sn' => $bicycle_sn,
                'region_id' => $region['region_id']
            );

            $device_info = $this->sys_model_bicycle->getBicycleInfo($condition, 'bicycle_sn, lock_sn');
            if (empty($device_info)) {
                $this->response->showErrorResult('系统不存在此单车编号');
            }
            if (empty($device_info['lock_sn'])) {
                $this->response->showErrorResult('此单车未绑定锁');
            }
            $this->request->post['device_id'] = $device_info['lock_sn'];
        }
    }
}