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

        // 当前网址
        $cur_url = isset($this->request->get['route']) ? $this->request->get['route'] : $this->config->get('action_default');
        if(substr_count($cur_url,'/') > 1) $cur_url = substr($cur_url,0,strrpos($cur_url,'/'));

        $this->load->library('sys_model/menu', true);
        $menu_id = $this->sys_model_menu->getMenuInfo(array('menu_action'=>$cur_url))['menu_id'];

        //菜单头部显示
        $join = array(
            'region' => 'region.region_id=bicycle.region_id',
            'cooperator' => 'cooperator.cooperator_id=bicycle.cooperator_id'
        );
        $this->load->library('sys_model/bicycle', true);
        $total = $this->sys_model_bicycle->getTotalBicycles("",$join);
        // 使用中单车数
        $condition = array(
            'is_using' => 2
        );
        $using_bicycle = $this->sys_model_bicycle->getTotalBicycles($condition);
        // 故障单车数
        $condition = array(
            'is_using' => 1
        );
        $fault_bicycle = $this->sys_model_bicycle->getTotalBicycles($condition);

        $this->load->library('sys_model/admin_menu_collect', true);

//        $this->load->common['http_server'] = HTTP_SERVER;
        $this->load->common['total_bicycle'] = $total;
        $this->load->common['using_bicycle'] = $using_bicycle;
        $this->load->common['fault_bicycle'] = $fault_bicycle;
        $this->load->common['cur_url'] = $cur_url;
        $this->load->common['menu_id'] = $menu_id;
        $this->load->common['menu_collect_status'] = $this->sys_model_admin_menu_collect->getCollect(array('menu_id'=>$menu_id))['status'];
    }
}