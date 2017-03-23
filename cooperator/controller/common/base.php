<?php
class ControllerCommonBase extends Controller {

    public function redirect($url) {
        $data = array(
            'url' => $url
        );
        echo $this->load->view('common/redirect', $data);
        exit();
    }
}