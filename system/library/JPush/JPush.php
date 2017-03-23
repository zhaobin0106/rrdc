<?php
namespace JPush;

require_once 'vendor/autoload.php';

use \JPush\Model as M;
use \JPush\JPushClient;
use \JPush\Exception\APIRequestException;

class JPush {
    private $_client;

    public function __construct($registry)
    {
        $this->config = $registry->get('config');
        if (!$this->config->get('config_jpush_app_key') || !$this->config->get('config_jpush_master_secret')) {
            throw new \Exception('缺少配置');
        }

        $config['config_jpush_app_key'] = $this->config->get('config_jpush_app_key');
        $config['config_jpush_master_secret'] = $this->config->get('config_jpush_master_secret');
        $this->set_client($config);
    }

    public function set_client($config) {
        $this->_client = new JPushClient($config['config_jpush_app_key'], $config['config_jpush_master_secret']);
    }

    public function get_client() {
        return $this->_client;
    }

    /**
     * 指定客户端推送通知
     * @param array|string $account_ids 客户端ID
     * @param string $msg 消息体json
     * @return bool
     * @throws Exception
     */
    public function notify($account_ids, $msg) {
        if (is_null($this->_client)) {
            return false;
        }

        if (!is_array($account_ids)) {
            $account_ids = ["$account_ids"];
        }

        try {
            $result = $this->_client->push()
                ->setPlatform(M\all)
                ->setAudience(M\audience(M\alias($account_ids)))
                ->setNotification(M\notification($msg))
                ->send();
        } catch (APIRequestException $e) {
            throw new Exception($e->getMessage());
        }
        return $result;
    }

    /**
     * 指定客户端推送消息
     * @param array|string $account_ids 客户端id
     * @param string $msg 消息体json
     * @return bool
     * @throws Exception
     */
    public function message($account_ids, $msg) {
        if (is_null($this->_client)) {
            return false;
        }
        if (!is_array($account_ids)) {
            $account_ids = ["$account_ids"];
        }
        try {
            $result = $this->_client->push()
                ->setPlatform(M\all)
                ->setAudience(M\audience(M\alias($account_ids)))
                ->setMessage(M\Message($msg))
                ->send();
        } catch (APIRequestException $e) {
            throw new \Exception($e->getMessage());
        }
        return $result;
    }

    /**
     * 全部客户端推送通知
     * @param string $msg 消息体json
     * @return bool
     * @throws Exception
     */
    public function notify_all($msg) {
        if (!is_null($this->_client)) {
            return false;
        }
        try {
            $result = $this->_client->push()
                ->setPlatform(M\all)
                ->setAudience(M\all)
                ->setNotification(M\notification($msg))
                ->send();
        } catch (APIRequestException $e) {
            throw new \Exception($e->getMessage());
        }
        return $result;
    }

    /**
     * 全部客户端推送消息
     * @param $msg
     * @return bool
     * @throws Exception
     */
    public function message_all($msg) {
        if (is_null($this->_client)) {
            return false;
        }
        try {
            $result = $this->_client->push()
                ->setPlatform(M\all)
                ->setAudience(M\all)
                ->setMessage(M\Message($msg))
                ->send();
        } catch (APIRequestException $e) {
            throw new Exception($e->getMessage());
        }
        return $result;
    }
}