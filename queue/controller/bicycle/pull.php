<?php
class ControllerBicyclePull extends Controller {
    public function index() {
        if (ob_get_level()) ob_end_clean();

//        $model_queue = new \Sys_Model\Queue($this->registry);
        $model_queue = new \Logic\Queue($this->registry);

        $worker = new \Queue\Queue_Server();
        while (true) {
            $list_key = $worker->scan();
            if (!empty($list_key) && is_array($list_key)) {
                //foreach ($list_key as $key) {
                    $content = $worker->pop($list_key, 0);
                    if (empty($content)) continue;
                    $method = key($content);
                    $arg = current($content);
                    $model_queue->$method($arg);
                    echo date('Y-m-d H:i:s', time()) . ' ' . $method . "\n";
//                    flush();
//                    ob_flush();
                //}
            }
            sleep(1);
        }
    }
}