<?php
namespace Logic;
use Sys_Model\Location_Records;

class Queue {
    private $registry;

    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param $data
     * @return array
     */
    public function addLocation($data) {
        $location_record = new \Sys_Model\Location_Records($this->registry);
        $location_record->addLogs($data);
        return callback(true);
    }

    //退款

    //极光推送

}