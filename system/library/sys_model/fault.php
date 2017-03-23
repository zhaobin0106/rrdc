<?php
namespace Sys_Model;

class Fault {
    private $memcache;

    public function __construct($registry) {
        $this->db = $registry->get('db');
    }

    // ****************************************** 设备故障表 ******************************************
    // ------------------------------------------ 写 ------------------------------------------
    /**
     * 添加故障记录
     * @param $data
     * @return mixed
     */
    public function addFault($data) {
        return $this->db->table('fault')->insert($data);
    }

    /**
     * 更新故障记录
     * @param $where
     * @param $data
     * @return mixed
     */
    public function updateFault($where, $data) {
        return $this->db->table('fault')->where($where)->update($data);
    }

    /**
     * 删除故障记录
     * @param $where
     * @return mixed
     */
    public function deleteFault($where) {
        return $this->db->table('fault')->where($where)->delete();
    }

    // ------------------------------------------ 读 ------------------------------------------
    // 故障表
    /**
     * 获取故障记录列表
     * @param array $where
     * @param string $order
     * @param string $limit
     * @return mixed
     */
    public function getFaultList($where = array(), $order = '', $limit = '') {
        return $this->db->table('fault')->where($where)->order($order)->limit($limit)->select();
    }

    /**
     * 获取故障记录信息
     * @param $where
     * @return mixed
     */
    public function getFaultInfo($where) {
        return $this->db->table('fault')->where($where)->limit(1)->find();
    }

    /**
     * 统计故障记录信息
     * @param $where
     * @return mixed
     */
    public function getTotalFaults($where) {
        return $this->db->table('fault')->where($where)->limit(1)->count(1);
    }

    // 故障类型表
    /**
     * 获取故障类型列表
     * @param array $where
     * @param string $order
     * @param string $limit
     * @return mixed
     */
    public function getFaultTypeList($where = array(), $order = '', $limit = '') {
        $rec =  $this->db->table('fault_type')->where($where)->order($order)->limit($limit)->select();
        return $rec;
    }

    /**
     * 获取故障类型信息
     * @param $where
     * @return mixed
     */
    public function getFaultTypeInfo($where) {
        return $this->db->table('fault_type')->where($where)->limit(1)->find();
    }

    /**
     * 统计故障类型信息
     * @param $where
     * @return mixed
     */
    public function getTotalFaultTypes($where) {
        return $this->db->table('fault_type')->where($where)->limit(1)->count();
    }

    /**
     * 获取故障类型列表
     * @param array $where
     * @param string $order
     * @param string $limit
     * @return array|string
     */
    public function getAllFaultType($where = array(), $order = 'display_order desc, fault_type_id asc', $limit = '') {
        $result = $this->memcache->get('ebike_fault_type');
        if (!$result) {
            $result = $this->db->table('fault_type')->where($where)->order($order)->limit($limit)->select();
            $this->memcache->set('ebike_fault_type', $result);
        }
        return $result;
    }

    // ****************************************** 违规停放表 ******************************************
    // ------------------------------------------ 写 ------------------------------------------
    /**
     * 添加故障记录
     * @param $data
     * @return mixed
     */
    public function addIllegalParking($data) {
        return $this->db->table('illegal_parking')->insert($data);
    }

    /**
     * 更新故障记录
     * @param $where
     * @param $data
     * @return mixed
     */
    public function updateIllegalParking($where, $data) {
        return $this->db->table('illegal_parking')->where($where)->update($data);
    }

    /**
     * 删除故障记录
     * @param $where
     * @return mixed
     */
    public function deleteIllegalParking($where) {
        return $this->db->table('illegal_parking')->where($where)->delete();
    }

    // ------------------------------------------ 读 ------------------------------------------
    // 故障表
    /**
     * 获取故障记录列表
     * @param array $where
     * @param string $order
     * @param string $limit
     * @return mixed
     */
    public function getIllegalParkingList($where = array(), $order = '', $limit = '') {
        return $this->db->table('illegal_parking')->where($where)->order($order)->limit($limit)->select();
    }

    /**
     * 获取故障记录信息
     * @param $where
     * @return mixed
     */
    public function getIllegalParkingInfo($where) {
        return $this->db->table('illegal_parking')->where($where)->limit(1)->find();
    }

    /**
     * 统计故障记录信息
     * @param $where
     * @return mixed
     */
    public function getTotalIllegalParking($where) {
        return $this->db->table('illegal_parking')->where($where)->limit(1)->count(1);
    }

}