<?php
class ControllerStartupBase extends Controller {
    public function index() {
        if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $this->load->common = array(
                'header' => '',
                'footer' => ''
            );
        } else {
            $this->load->common = array(
                'header' => $this->load->controller('common/header'),
                'footer' => $this->load->controller('common/footer')
            );
        }
    }
}