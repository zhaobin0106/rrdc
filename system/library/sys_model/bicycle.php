<?php
namespace Sys_Model;

class Bicycle {
    public function __construct($registry)
    {
        $this->db = $registry->get('db');
    }

    // ------------------------------------------------- 写 -------------------------------------------------
    /**
     * 添加单车
     * @param $data
     * @return mixed
     */
    public function addBicycle($data) {
        return $this->db->table('bicycle')->insert($data);
    }

    /**
     * 更新单车
     * @param $where
     * @param $data
     * @return mixed
     */
    public function updateBicycle($where, $data) {
        return $this->db->table('bicycle')->where($where)->update($data);
    }

    /**
     * 删除单车
     * @param $where
     * @return mixed
     */
    public function deleteBicycle($where) {
        return $this->db->table('bicycle')->where($where)->delete();
    }

    // ------------------------------------------------- 读 -------------------------------------------------
    /**
     * 获取单车列表
     * @param array $where
     * @param string $order
     * @param string $limit
     * @return mixed
     */
    public function getBicycleList($where = array(), $order = '', $limit = '', $field = 'bicycle.*', $join = array()) {
        $table = 'bicycle as bicycle';
        if (is_array($join) && !empty($join)) {
            $addTables = array_keys($join);
            $joinType = '';
            if (!empty($addTables) && is_array($addTables)) {
                foreach ($addTables as $v) {
                    $table .= sprintf(',%s as %s', $v, $v);
                    $joinType .= ',left';
                }
            }
            $on = implode(',', $join);

            $this->db->join($joinType)->on($on);
        }

        return $this->db->table($table)->field($field)->where($where)->order($order)->limit($limit)->select();
    }

    /**
     * 获取单车信息
     * @param $where
     * @param string $field
     * @return mixed
     */
    public function getBicycleInfo($where, $field = '*') {
        return $this->db->table('bicycle')->where($where)->field($field)->limit(1)->find();
    }

    /**
     * 统计单车信息
     * @param $where
     * @return mixed
     */
    public function getTotalBicycles($where, $join = array()) {
        $table = 'bicycle as bicycle';
        if (is_array($join) && !empty($join)) {
            $addTables = array_keys($join);
            $joinType = '';
            if (!empty($addTables) && is_array($addTables)) {
                foreach ($addTables as $v) {
                    $table .= sprintf(',%s as %s', $v, $v);
                    $joinType .= ',left';
                }
            }
            $on = implode(',', $join);

            $this->db->join($joinType)->on($on);
        }

        return $this->db->table($table)->where($where)->limit(1)->count(1);
    }

    /**
     * 获取单车位置信息
     */
    public function getBicycleLockMarker($where = array(), $field = '', $limit = '') {
        $field .= 'b.bicycle_id,b.bicycle_sn,b.type,b.fee,r.region_id,r.region_name,r.region_city_code,r.region_city_ranking,';
        $field .= 'l.lock_sn,l.lat,l.lng';
        $on = 'b.lock_sn=l.lock_sn,b.region_id=r.region_id';
        $result = $this->db->table('bicycle as b,lock as l,region as r')->where($where)->field($field)->join('left,left')->on($on)->limit($limit)->select();
        return $result;
    }


    /**
     * 获取指定边界内的单车列表
     * @param $min_lat
     * @param $min_lng
     * @param $max_lat
     * @param $max_lng
     * @param $status 状态选择，可以是low_battery,illegal_parking,fault,offline等字符串中的一个或者多个所组成的数组
     * @return mixed
     */
    public function getBicyclesByBounds($min_lat, $min_lng, $max_lat, $max_lng, $status) {
        $lng_bound = ($min_lng <= $max_lng) ?
            "AND l.lng>=$min_lng AND l.lng<=$max_lng "
            :  // 注意地图在经度方向是可以拼接的（-180°跟+180°拼接在一起），所以出现左边的经度大于右边的经度是很正常的
            "AND ((l.lng>=$min_lng AND l.lng<=180) OR (l.lng>=-180 AND l.lng<=$max_lng))";
        $sql = "SELECT distinct(b.bicycle_id), b.bicycle_sn, b.type, b.fee,b.cooperator_id, r.region_id, r.region_name,"
            ." r.region_city_code, r.region_city_ranking, l.lock_sn, l.lat, l.lng, l.lock_status, l.battery,l.system_time,"
            ." from_unixtime(l.system_time,'%Y-%m-%d %H:%i:%s') as last_update, b.fault, b.illegal_parking, b.low_battery, "
            ."(l.system_time>=unix_timestamp()-" . OFFLINE_THRESHOLD . ") as online, "
            ."(l.system_time<unix_timestamp()-" . OFFLINE_THRESHOLD . ") as offline, "
            ."round(l.gz/100) as gprs, l.gz%100 as gps, FORMAT(l.gx/100,2) as battery_voltage, FORMAT(l.gy/100,2) as charging_voltage, "
            ."l.serialnum, (l.serialnum < 64 AND (l.serialnum & 32)=32) as charging,  (l.serialnum < 64 AND (l.serialnum & 16)=16) as moving, "
            ."(l.serialnum < 64 AND (l.serialnum & 8)=8) as closed, (l.serialnum < 64 AND (l.serialnum & 4)=4) as low_battary_alarm, "
            ."(l.serialnum < 64 AND (l.serialnum & 2)=2) as illegal_moving_alarm, (l.serialnum < 64 AND (l.serialnum & 1)=1) as gps_positioning "
            ."FROM rich_bicycle as b "
            ."LEFT JOIN rich_lock as l ON b.lock_sn=l.lock_sn "
            ."LEFT JOIN rich_region as r ON b.region_id=r.region_id "
            ."WHERE l.lat<>'' AND l.lng<> '' AND l.lat>=$min_lat AND l.lat<=$max_lat " . $lng_bound
            .$this->_parseStatus($status);
        return $this->db->getRows($sql);
    }

    /**
     * 分析status变量获取数据库查询的where条件
     * @param $status
     */
    private function _parseStatus($status) {
        if(!is_array($status) && !is_string($status)) return '';
        if(is_string($status)) $status = array($status);
        $ss = array();
        foreach ($status as $s) {
            if(in_array($s, array('low_battery','illegal_parking','fault')))
                $ss[] = $s . '=1';
            else if($s=='offline') {
                $ss[] = '(l.system_time<unix_timestamp()-3610)';
            }
        }
        return !empty($ss) ? ' AND ' . implode(" AND ", $ss) : '';
    }
}
