<?php
class ControllerCommonHeader extends Controller {
    public function index() {

        if ($this->request->server['HTTPS']) {
            $data['base'] = HTTPS_SERVER;
        } else {
            $data['base'] = HTTP_SERVER;
        }

        $this->assign('static', HTTP_IMAGE);
        $this->assign('app_name', $this->config->get('config_app_name'));
        $this->assign('title', $this->document->getTitle());
        $this->assign('description', $this->document->getDescription());
        $this->assign('keywords', $this->document->getKeywords());
        $this->assign('links', $this->document->getLinks());
        $this->assign('styles', $this->document->getStyles());
        $this->assign('scripts', $this->document->getScripts());
        $this->assign('lang', $this->language->get('code'));
        $this->assign('direction', $this->language->get('direction'));
        $this->assign('logout_url', $this->url->link('common/logout'));

        // 菜单
        $this->assign('menu', $this->url->link('common/menu'));

        // 菜单
        $this->assign('menu', $this->load->controller('common/menu'));

        $this->load->language('common/header');
        $languages = $this->language->all();
//        $this->assign('heading_title', $this->language->get('heading_title'));
        $this->assign($languages);
        return $this->load->view('common/header', $this->output);
    }
}