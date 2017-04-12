<?php
/**
 * Created by PhpStorm.
 * User: h
 * Date: 2016/12/26
 * Time: 14:55
 */

/**
 * 单车类型
 */
if (!function_exists('get_bicycle_type'))
{
    function get_bicycle_type() {
        return array(
            '1' => '单人车',
            '2' => '双人车',
            '3' => '家庭车',
        );
    }
}

/**
 * 锁状态
 */
if (!function_exists('get_lock_status'))
{
    function get_lock_status() {
        return array(
            '0' => '已关锁',
            '1' => '开锁',
            '2' => '异常'
        );
    }
}

/**
 * 充值类型
 */
if (!function_exists('get_recharge_type'))
{
    function get_recharge_type() {
        return array(
            '0' => '余额充值',
            '1' => '押金充值'
        );
    }
}

/**
 * 支付状态
 */
if (!function_exists('get_payment_state'))
{
    function get_payment_state() {
        return array(
            '0' => '未支付',
            '1' => '已支付',
            '-1' => '已退款',
        );
    }
}

/**
 * 订单状态
 */
if (!function_exists('get_order_state'))
{
    function get_order_state() {
        return array(
            '-1' => '已取消',
            '0' => '未生效',
            '1' => '进行中',
            '2' => '已完成'
        );
    }
}

/**
 * 订单状态
 */
if (!function_exists('get_parking_type'))
{
    function get_parking_type() {
        return array(
            '1' => '违停上报',
            '2' => '其他上报'
        );
    }
}

/**
 * 订单状态
 */
if (!function_exists('get_cooperator_state'))
{
    function get_cooperator_state() {
        return array(
            '0' => '禁用',
            '1' => '启用',
        );
    }
}

/**
 * 设置类型状态
 */
if (!function_exists('get_setting_boolean'))
{
    function get_setting_boolean() {
        return array(
            '0' => '禁用',
            '1' => '启用',
        );
    }
}

/**
 * 常规布尔型
 */
if (!function_exists('get_common_boolean'))
{
    function get_common_boolean() {
        return array(
            '1' => '是',
            '0' => '否',
        );
    }
}

/**
 * 申请结果
 */
if (!function_exists('get_common_result'))
{
    function get_common_result() {
        return array(
            '1' => '通过',
            '0' => '不通过',
        );
    }
}

/**
 * 故障处理
 */
if (!function_exists('get_fault_processed'))
{
    function get_fault_processed() {
        return array(
            '1' => '已处理',
            '0' => '未处理',
        );
    }
}