<?php
namespace Sys_Model;
class Sms {
    public function __construct($registry)
    {
        $this->db = $registry->get('db');
    }

    public function addSms($data) {
        return $this->db->table('sms')->insert($data);
    }

    public function getSmsInfo($where) {
        return $this->db->table('sms')->where($where)->find();
    }

    public function updateSmsStatus($where, $status = 1) {
        return $this->db->table('sms')->where($where)->update(array('state' => $status));
    }

    public function delete($where) {
        return $this->db->table('sms')->where($where)->delete();
    }
}