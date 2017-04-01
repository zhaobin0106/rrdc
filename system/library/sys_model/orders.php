<?php
namespace Sys_Model;

class Orders {
    public function __construct($registry) {
        $this->db = $registry->get('db');
    }

    public function addOrders($data) {
        return $this->db->table('orders')->insert($data);
    }

    public function getOrdersList($where, $order = '', $limit = '', $field = '*') {
        return $this->db->table('orders')->field($field)->where($where)->order($order)->limit($limit)->select();
    }

    public function getTotalOrders($where) {
        return $this->db->table('orders')->where($where)->count('order_id');
    }

    public function getOrdersInfo($where) {
        return $this->db->table('orders')->field('*')->where($where)->find();
    }

    public function addOrderLine($data) {
        return $this->db->table('orders_line')->insert($data);
    }

    public function getOrderLine($where, $fields = '*', $order = 'add_time asc', $limit = '') {
        return $this->db->table('orders_line')->field($fields)->where($where)->order($order)->limit($limit)->select();
    }

    public function updateOrders($where, $data) {
        return $this->db->table('orders')->where($where)->update($data);
    }

    public function deleteOrders($where) {
        return $this->db->table('orders')->where($where)->delete();
    }

    public function getLastSql() {
        return $this->db->getLastSql();
    }

    public function begin() {
        $this->db->begin();
    }

    public function commit() {
        $this->db->commit();
    }

    public function rollback() {
        $this->db->rollback();
    }
}