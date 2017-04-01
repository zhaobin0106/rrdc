<?php
/**
 * 东莞市亦强软件有限公司
 * Author: 罗剑波
 * Time: 2017/3/4 13:40
 */
class ControllerSystemTest extends Controller {

    public function index() {
        $device_id = '063070627746';
//        $device_id = '063072656941';

        $this->load->library('instructions/instructions', true);
        $this->instructions_instructions->setGapTime($device_id, 180);
        $this->instructions_instructions->setGapTime2($device_id, 180);
    }

}