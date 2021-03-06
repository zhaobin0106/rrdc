<?php

class ControllerTransferOperator extends Controller
{
    /**
     * 指令回调地址
     */
    public function propelling()
    {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $post = file_get_contents("php://input");
            date_default_timezone_set('PRC');
            file_put_contents(DIR_BASE.'/transfer/controller/transfer/reply.log', date('Y-m-d H:i:s ') . $post . "\n", FILE_APPEND);
            if(strpos($post, 'open')!==FALSE) {
                file_put_contents(DIR_BASE.'/transfer/controller/transfer/open_reply.log', date('Y-m-d H:i:s ') . $post . "\n", FILE_APPEND);
            }
//            $post = $this->request->post;
            if (empty($post)) {
                die('empty post');
            }

            $this->request->post = json_decode($post, true);
            $user_id = $this->request->post['userid'];
            $cmd = $this->request->post['cmd'];
            $device_id = $this->request->post['deviceid'];
            $result = $this->request->post['result'];
            $info = $this->request->post['info'];
            $serialnum = $this->request->post['serialnum'];
            $open_time = time();
            $sign = $this->request->post['sign'];

            $data = array (
                'cmd' => $cmd,
                'cooperator_id' => $user_id,
                'device_id' => $device_id,
                'result' => $result,
                'info' => $info,
                'serialnum' => $serialnum,
                'open_time' => $open_time
            );

            $this->load->library('logic/orders', true);
            //接收指令回调

            $this->load->library('sys_model/instruction', true);
            $result = $this->sys_model_instruction->addInstructionRecord($data);
//            file_put_contents('ucc.log', json_encode($data), 8);
            switch (strtolower($data['cmd'])) {
                case 'open':
                    $result = $this->logic_orders->effectOrders($data);
                    if ($result['state'] == true) {
                        $arr = $this->response->_error['success'];
                        $arr['data'] = $result['data'];
                        $this->load->library('JPush/JPush', true);
                        $send_result = $this->JPush_JPush->message($result['data']['user_id'], json_encode($arr));
                        file_put_contents('jpush_log.txt', $send_result, FILE_APPEND);
                    }
                    break;
                case 'select':
                    break;
                case 'close':
                    break;
                case 'beep':
                    break;
            }

            if ($result['state']) {
                $this->response->showSuccessResult('', 'operator success!');
            } else {
                $this->response->showErrorResult($result['msg']);
            }
        } else {
            $this->response->showErrorResult('Request require post!');
        }
    }
}
