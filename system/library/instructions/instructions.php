<?php
/**
 * Created by PhpStorm.
 * User: estronger
 * Date: 2016/12/8
 * Time: 10:14
 */
namespace Instructions;
use Tool\Curl;

class Instructions {

    private $gap_time = 0;

    private $curl;

    public function __construct($registry)
    {
        $this->curl = new Curl(API_URL);
    }

    /**
     * 下发指令，指令的类型有select,open,close,gapTime
     * @param array $data 键值为cmd和device_id
     * @return mixed
     */
    public function sendInstruct($data) {
        $type = $data['cmd'];
        $type = strtolower($type);
        $base = array(
            'userid' => USER_ID,
            'cmd' => $data['cmd'],
            'deviceid' => $data['device_id']
        );
        if (in_array($type, array('select', 'open', 'close', 'beep'))) {
            $base['serialnum'] = $this->gap_time ? $this->gap_time : $this->make_sn();
        } else {
            $base['serialnum'] = $this->gap_time ? $this->gap_time : GAP_TIME;
        }

        $base['sign'] = $this->make_md5($base);
//        print_r($base);exit;
        $base = json_encode($base);
        $this->curl->setData($base);
        $response = $this->curl->postData();
        return $response;
    }

    public function parseLock($device_id, $cmd) {
        $data['device_id'] = $device_id;
        $data['cmd'] = $cmd;
        $response = $this->sendInstruct($data);
        return $response;
    }

    /**
     * 开锁
     * @param $device_id
     * @param $time
     * @return mixed
     */
    public function openLock($device_id, $time = 0) {
        if ($time > 0) {
            $this->gap_time = $time;
        }
        $response = $this->parseLock($device_id, 'open');
        $arr = $this->jsonToArray($response);
        if (strtolower($arr['result']) == 'ok') {
            return callback(true);
        }
        return callback(false, '发送失败', $arr);
    }

    /**
     * 关锁
     * @param $device_id
     * @return mixed
     */
    public function closeLock($device_id) {
        $response = $this->parseLock($device_id, 'close');
        return $response;
    }

    /**
     * 寻车，发送车响的警报
     * @param $device_id
     * @return string $response
     */
    public function beepLock($device_id) {
        $response = $this->parseLock($device_id, 'beep');
        return $response;
    }

    /**
     * 查询数据
     * @param $data
     * @return bool|mixed
     */
    public function selectLocks($data) {
        if (empty($data)) {
            return false;
        }
        if (is_array($data)) {
            $device_id = implode(',', $data); //以逗号隔开
        } else {
            $device_id = $data;
        }
        $response = $this->parseLock($device_id, 'select');
        return $response;
    }

    /**
     * 设置设备锁开时位置回传间隔
     * @param $device_id
     * @param $time
     */
    public function setGapTime($device_id, $time) {
        $this->gap_time = $time;
        $this->parseLock($device_id, 'gapTime');
    }

    /**
     * 设置设备锁关是位置回传间隔
     * @param $device_id
     * @param $time
     */
    public function setGapTime2($device_id, $time) {
        $this->gap_time = $time;
        $this->parseLock($device_id, 'gapTime2');
    }

    /**
     * 生成流水号，直接时间戳
     * @return int
     */
    public function make_sn() {
        return time();
    }

    /**
     * MD5签名
     * @param $data
     * @return string
     */
    public function make_md5($data) {
        $str = '';
        if (!empty($data)) {
            $str .= implode('', $data);
        }
        $str = $str . USER_KEY;
        return md5($str);
    }

    public function jsonToArray($json) {
        return json_decode($json, true);
    }
}