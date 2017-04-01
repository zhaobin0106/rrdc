<?php
class ControllerLocationLocation extends Controller {
    public function getBicycleLocation() {
        if (!isset($this->request->post['lng']) || !isset($this->request->post['lat'])) {
            $this->response->showErrorResult($this->language->get('error_missing_parameter'),1);
        }
        $lat = $this->request->post['lat'];
        $lng = $this->request->post['lng'];

        $this->load->library('tool/polygon');

        //距离单车点的距离（不进行坐标点的转换）
        //2公里
        $distance = 2;
        $this->load->library('tool/distance');
        $arr = $this->tool_distance->getRange($lat, $lng, $distance);

        $zoom = $this->request->post['zoom'];
        if ($zoom < 15) {
            $this->response->showErrorResult($this->language->get('error_map_zoom'),125);
        }

        $this->load->library('sys_model/bicycle');
        $where = array();
        $where['l.lock_status'] = '0';
        $where['b.type'] = 1;

        $where['l.lat'] = array(
            array('gt', $arr['min_lat']),
            array('lt', $arr['max_lat'])
        );

        $where['l.lng'] = array(
            array('gt', $arr['min_lng']),
            array('lt', $arr['max_lng'])
        );

        $result = $this->sys_model_bicycle->getBicycleLockMarker($where);

        $data = array();
        if (is_array($result) && !empty($result)) {
            foreach ($result as $item) {
                $item['area_code'] = sprintf('%03d%02d', $item['region_city_code'], $item['region_city_ranking']);
                $data[] = $item;
            }
        }

        $this->response->showSuccessResult($data);
    }

    public function getLocalPrice() {
        $city_code = isset($this->request->post['city_code']) ? $this->request->post['city_code'] : '';

        if (strlen($city_code) == 4) {
            $city_code = substr($city_code, -3);
        }

        $this->load->library('sys_model/region');
        $this->load->library('tool/polygon');

        $cur_lat = isset($this->request->post['cur_lat']) ? $this->request->post['cur_lat'] : '0';
        $cur_lng = isset($this->request->post['cur_lng']) ? $this->request->post['cur_lng'] : '0';

        $this->load->library('tool/polygon', true);
        $where = $city_code ? array('region_city_code' => $city_code) : array();
        $region_list = $this->sys_model_region->getRegionList($where);

        $storage_list = array();

        foreach ($region_list as $region) {
            $northeast['lng'] = $region['region_bounds_northeast_lng'];
            $northeast['lat'] = $region['region_bounds_northeast_lat'];

            $southwest['lng'] = $region['region_bounds_southwest_lng'];
            $southwest['lat'] = $region['region_bounds_southwest_lat'];

            $northeast_southwest = array($northeast, $southwest);

            $isInRegion = $this->tool_polygon->pointIsInRegion($cur_lng, $cur_lat, $northeast_southwest);

            if (!$isInRegion) { continue; }

            if (strlen($region['region_bounds']) == 2) continue;

            $storage_list[] = array(
                'region_charge_time' => $region['region_charge_time'],
                'region_charge_fee' => $region['region_charge_fee']
            );
        }

        $arr_data = array();

        if (empty($storage_list)) {
            $arr_data['price'] = $this->config->get('config_price_unit');
            $unit = $this->config->get('config_time_charge_unit');
            $f_unit = strval($unit / 3600);
            $arr_data['unit'] = $f_unit;
        } else {
            $arr_data['price'] = $storage_list[0]['region_charge_fee'];
            $arr_data['unit'] = strval($storage_list[0]['region_charge_time'] / 60);
        }

        $this->response->showSuccessResult($arr_data);
    }
}