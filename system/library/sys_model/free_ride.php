<?php
/**
 * 设置免费骑行
 */
namespace Sys_Model;
class Free_ride {
    public function __construct($registry) {
        $this->db = $registry->get('db');
    }

    public function addFreeTime($data) {
        return $this->db->table('free_time')->insert($data);
    }

    public function getFreeTime($where) {
        return $this->db->table('free_time')->where($where)->find();
    }
}