<?php
namespace Sys_Model;

class Region {
    public function __construct($registry)
    {
        $this->db = $registry->get('db');
    }

    // ------------------------------------------------- 写 -------------------------------------------------
    /**
     * 添加景区
     * @param $data
     * @return mixed
     */
    public function addRegion($data) {
        return $this->db->table('region')->insert($data);
    }

    /**
     * 更新景区
     * @param $where
     * @param $data
     * @return mixed
     */
    public function updateRegion($where, $data) {
        return $this->db->table('region')->where($where)->update($data);
    }

    /**
     * 删除景区
     * @param $where
     * @return mixed
     */
    public function deleteRegion($where) {
        return $this->db->table('region')->where($where)->delete();
    }

    // ------------------------------------------------- 读 -------------------------------------------------
    /**
     * 获取景区列表
     * @param array $where
     * @param string $order
     * @param string $limit
     * @return mixed
     */
    public function getRegionList($where = array(), $order = '', $limit = '', $field = 'r.*', $join = array()) {
        $table = 'region as r';
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
     * 获取景区信息
     * @param $where
     * @return mixed
     */
    public function getRegionInfo($where) {
        return $this->db->table('region')->where($where)->limit(1)->find();
    }

    /**
     * 统计景区信息
     * @param $where
     * @return mixed
     */
    public function getTotalRegions($where, $join = array()) {
        $table = 'region as r';
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
     * 获取景区最大信息
     * @param $where
     * @return mixed
     */
    public function getMaxRegions($where, $field) {
        return $this->db->table('region')->where($where)->limit(1)->max($field);
    }

    /**
     * 获取景区位置信息
     */
    public function getRegionLockMarker($where = array(), $field = '', $limit = '') {
        $field .= 'b.region_id,b.region_sn,b.type,b.fee,b.scenic_spot_id,b.scenic_spot_name,';
        $field .= 'l.lock_sn,l.lat,l.lng';
        $on = 'b.lock_sn=l.lock_sn';
        $result = $this->db->table('region as b,lock as l')->where($where)->field($field)->join('left')->on($on)->limit($limit)->select();
        return $result;
    }
}
