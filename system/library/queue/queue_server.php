<?php
namespace Queue;
class Queue_Server {
    private $_queueDb;

    public function __construct()
    {
        $this->_queueDb = new Queue_Db();
    }

    /**
     * 取出队列
     * @param $key
     * @param $time
     * @return string
     */
    public function pop($key, $time = '') {
        return unserialize($this->_queueDb->pop($key, $time));
    }

    public function scan() {
        return $this->_queueDb->scan();
    }
}