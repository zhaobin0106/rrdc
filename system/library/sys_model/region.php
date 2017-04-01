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


    // ******************************************** 管理员与景区对应表 ********************************************
    // -------------------------------------------- 写 --------------------------------------------
    /**
     * 添加合伙人景区
     * @param $data
     * @return mixed
     */
    public function addAdminToRegion($data) {
        return $this->db->table('admin_to_region')->insert($data);
    }

    /**
     * 删除合伙人景区
     * @param $where
     * @return mixed
     */
    public function deleteAdminToRegion($where) {
        return $this->db->table('admin_to_region')->where($where)->delete();
    }

    // -------------------------------------------- 读 --------------------------------------------
    /**
     * 获取合伙人景区列表
     * @param array $where
     * @param string $order
     * @param string $limit
     * @return mixed
     */
    public function getAdminToRegionList($where = array(), $order = '', $limit = '', $field = '*', $join = array()) {
        $table = 'admin_to_region as admin_to_region';
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
     * 统计合伙人景区信息
     * @param $where
     * @return mixed
     */
    public function getTotalAdminToRegions($where) {
        return $this->db->table('admin_to_region')->where($where)->limit(1)->count(1);
    }

    // ******************************************** 合伙人与景区对应表 ********************************************
    // -------------------------------------------- 写 --------------------------------------------
    /**
     * 添加合伙人景区
     * @param $data
     * @return mixed
     */
    public function addCooperatorToRegion($data) {
        return $this->db->table('cooperator_to_region')->insert($data);
    }

    /**
     * 删除合伙人景区
     * @param $where
     * @return mixed
     */
    public function deleteCooperatorToRegion($where) {
        return $this->db->table('cooperator_to_region')->where($where)->delete();
    }

    // -------------------------------------------- 读 --------------------------------------------
    /**
     * 获取合伙人景区列表
     * @param array $where
     * @param string $order
     * @param string $limit
     * @return mixed
     */
    public function getCooperatorToRegionList($where = array(), $order = '', $limit = '', $field = '*', $join = array()) {
        $table = 'cooperator_to_region as cooperator_to_region';
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
     * 统计合伙人景区信息
     * @param $where
     * @return mixed
     */
    public function getTotalCooperatorToRegions($where) {
        return $this->db->table('cooperator_to_region')->where($where)->limit(1)->count(1);
    }
}
