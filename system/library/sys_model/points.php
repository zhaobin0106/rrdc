<?php
namespace Sys_Model;

class Points {
    public function __construct($registry)
    {
        $this->db = $registry->get('db');
    }

    // ---------------------------------------------- 写 ----------------------------------------------
    /**
     * 添加信用积分
     * @param $data
     * @return mixed
     */
    public function addPoints($data) {
        return $this->db->table('points_log')->insert($data);
    }

    /**
     * 更新信用积分
     * @param $where
     * @param $data
     * @return mixed
     */
    public function updatePoints($where, $data) {
        return $this->db->table('points_log')->where($where)->update($data);
    }

    /**
     * 删除信用积分
     * @param $where
     * @return mixed
     */
    public function deletePoints($where) {
        return $this->db->table('points_log')->where($where)->delete();
    }

    // ---------------------------------------------- 读 ----------------------------------------------
    /**
     * 获取信用积分列表
     * @param array $where
     * @param string $order
     * @param string $limit
     * @return mixed
     */
    public function getPointsList($where = array(), $order = '', $limit = '') {
        $on = 'pl.user_id = u.user_id';
        return $this->db->table('points_log as pl,user as u')->where($where)->order($order)->limit($limit)->join('left')->on($on)->select();
    }

    /**
     * 获取信用积分信息
     * @param $where
     * @return mixed
     */
    public function getPointsInfo($where) {
        return $this->db->table('points_log')->where($where)->limit(1)->find();
    }

    /**
     * 统计信用积分信息
     * @param $where
     * @return mixed
     */
    public function getTotalPoints($where) {
        $on = 'pl.user_id = u.user_id';
        return $this->db->table('points_log as pl,user as u')->where($where)->limit(1)->join('left')->on($on)->count(1);
    }
}
