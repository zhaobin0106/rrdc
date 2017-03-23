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
}