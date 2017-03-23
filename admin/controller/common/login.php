<?php

/**
 * Class ControllerCommonLogin
 * 管理员登录
 */
class ControllerCommonLogin extends Controller {
    private $error = array();

    private $admin_id;
    private $admin_name;

    public function __construct($registry) {
        parent::__construct($registry);

        // 加载管理员 Model
        $this->load->library('sys_model/admin', true);
    }

    /**
     * 登录
     */
    public function index() {
        $this->load->language('common/login');

        if ($this->isLogged() && isset($this->request->cookie['token']) && ($this->request->cookie['token'] == $this->session->data['token'])) {
            $this->response->redirect($this->url->link('admin/index', '', true));
        }

        if (check_submit() && $this->validate()) {
            $token = token(32);
            $expire = TIMESTAMP + 86400;
            setcookie('token', $token, $expire);
            $this->session->data['token'] = $token;

            if (isset($this->request->post['redirect']) && (strpos($this->request->post['redirect'], HTTP_SERVER) === 0 || strpos($this->request->post['redirect'], HTTPS_SERVER) === 0)) {
                $this->response->redirect($this->request->post['redirect']);
            } else {
                $this->response->redirect($this->url->link('admin/index', '', true));
            }
        }

        $languages = $this->language->all();
        $this->assign($languages);
        $this->assign('is_login_page', true);
        $this->assign('static', HTTP_IMAGE);
        $this->assign('title', 'Ebike单车 | 登录');
        $this->assign('error', $this->error);

        $this->assign('action', $this->url->link('common/login', '', true));
        $this->assign('forgotten_url', $this->url->link('common/forgotten', '', true));
        $this->assign('header', $this->load->controller('common/header'));
        $this->assign('footer', $this->load->controller('common/footer'));
        $this->response->setOutput($this->load->view('common/login', $this->output));
    }

    /**
     * 登录数据验证
     * @return bool
     */
    public function validate() {
        if (!isset($this->request->post['username']) || !isset($this->request->post['password'])
            || !$this->login(
                $this->request->post['username'],
                html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')
            )) {
            $this->error['warning'] = '用户名或密码错误。';
        }

        return !$this->error;
    }

    /**
     * 登录，数据库操作
     * @param $admin_name
     * @param $password
     * @return mixed
     */
    function login($admin_name, $password) {
        $rec = $this->logic_admin->login($admin_name, $password);
        return $rec['state'];
    }

    /**
     * 是否已登录
     * @return mixed
     */
    public function isLogged() {
        return $this->admin_id;
    }
}