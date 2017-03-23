<?php
namespace Tool;
class Distance {
    public function getDistance($lng1, $lng2, $lat1, $lat2) {
        $lng1 = $lng1 * 0.01745329252;
        $lng2 = $lng2 * 0.01745329252;
        $lat1 = $lat1*0.01745329252;
        $lat2 = $lat2*0.01745329252;
        $d = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($lng1 - $lng2)) * 6370.6935;
        return $d;
    }

    /**
     * 获取范围点，数组
     * @param $lat float
     * @param $lng float
     * @param $distance string 单位是千米
     * @return array
     */
    public function getRange($lat, $lng, $distance) {
        $range = 180 / pi() * $distance / 6370.6935;
        $lng_range = $range / cos($lat * pi() / 180);
        $max_lat = $lat + $range;
        $min_lat = $lat - $range;
        $max_lng = $lng + $lng_range;
        $min_lng = $lng - $lng_range;

        return array(
            'max_lat' => $max_lat,
            'min_lat' => $min_lat,
            'max_lng' => $max_lng,
            'min_lng' => $min_lng
        );
    }

    public function sumDistance($data) {
        if (!is_array($data)) return 0;
        $count = count($data);
        if ($count <= 1) return 0;
        $distance = 0.00;
        for ($i = 0; $i < $count - 1; $i++) {
            $lng1 = $data[$i]['lng'];
            $lng2 = $data[$i + 1]['lng'];
            $lat1 = $data[$i]['lat'];
            $lat2 = $data[$i + 1]['lat'];
            $distance += $this->getDistance($lng1, $lng2, $lat1, $lat2);
        }
        return $distance;
    }

    public function getNearestPoint($lat, $lng, $data) {
        if (!is_array($data) || empty($data)) return 0;
        $i = 0;
        $arr = array();
        foreach ($data as $point) {
            $lat1 = $point['lat'];
            $lng1 = $point['lng'];
            $arr[$i] = $this->getDistance($lng, $lng1, $lat, $lat1);
            $i++;
        }
        if (!empty($arr)) {
            $new_arr = krsort($arr);
            return key(reset($new_arr));
        } else {
            return 0;
        }
    }
}