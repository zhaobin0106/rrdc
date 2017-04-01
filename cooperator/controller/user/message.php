<?php
class ControllerUserMessage extends Controller {
    private $cur_url = null;
    private $error = null;
    
    public function __construct($registry) {
        parent::__construct($registry);

        // 当前网址
        $this->cur_url = isset($this->request->get['route']) ? $this->url->link($this->request->get['route']) : '';

        // 加载coupon Model
        $this->load->library('sys_model/message', true);
        $this->load->library('sys_model/user', true);
    }

    /**
     * 系统消息列表
     */
    public function index() {
        $filter = array();
        $condition = array();
        
        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        $fields = 'm.*,user.mobile';
        $order = 'msg_time DESC';
        $rows = $this->config->get('config_limit_admin');
        $offset = ($page - 1) * $rows;
        $limit = sprintf('%d, %d', $offset, $rows);
        $join = array(
            'user' => 'user.user_id=m.user_id'
        );

        $result = $this->sys_model_message->getMessageList($condition, $fields, $order, $limit, $join);
        $total = $this->sys_model_message->getTotalMessages($condition, $join);

        if (is_array($result) && !empty($result)) {
            foreach ($result as &$item) {
                if ($item['user_id'] == '0') {
                    $item['user_name'] = '所用用户';
                } else {
                    $item['user_name'] = isset($item['mobile']) ? $item['mobile'] : '';
                }
                $item['msg_time'] = isset($item['msg_time']) && $item['msg_time'] > 0 ? date('Y-m-d H:i:s', $item['msg_time']) : '';
                $item['delete_action'] = $this->url->link('user/message/delete', 'msg_id='.$item['msg_id']);
                $item['info_action'] = $this->url->link('user/message/info', 'msg_id='.$item['msg_id']);
            }
        }

        $data_columns = $this->getDataColumns();
        $this->assign('data_columns', $data_columns);
        $this->assign('data_rows', $result);
        $this->assign('action', $this->cur_url);
        $this->assign('add_action', $this->url->link('user/message/add'));

        if (isset($this->session->data['success'])) {
            $this->assign('success', $this->session->data['success']);
            unset($this->session->data['success']);
        }

        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $page;
        $pagination->page_size = $rows;
        $pagination->url = $this->cur_url . '&amp;page={page}' . '&amp;' . str_replace('&', '&amp;', http_build_query($filter));
        $pagination = $pagination->render();
        $results = sprintf($this->language->get('text_pagination'), ($total) ? $offset + 1 : 0, ($offset > ($total - $rows)) ? $total : ($offset + $rows), $total, ceil($total / $rows));

        $this->assign('pagination', $pagination);
        $this->assign('results', $results);

        $this->response->setOutput($this->load->view('user/message_list', $this->output));
    }

    /**
     * 表格字段
     * @return mixed
     */
    protected function getDataColumns() {
        $this->setDataColumn('消息标题');
        $this->setDataColumn('用户');
        $this->setDataColumn('消息时间');
        return $this->data_columns;
    }

    /**
     * 添加系统消息
     */
    public function add() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $input = $this->request->post(array('msg_title', 'msg_image', 'mobiles', 'msg_abstract', 'msg_link', 'msg_content'));
            $now = time();


            $mobiles = explode(PHP_EOL, $input['mobiles']);
            if (is_array($mobiles) && !empty($mobiles)) {
                foreach ($mobiles as $mobile) {
                    $condition = array(
                        'mobile' => $mobile
                    );
                    $user = $this->sys_model_user->getUserInfo($condition, 'user_id');
                    if ($user) {
                        $data = array(
                            'user_id' => $user['user_id'],
                            'msg_time' => $now,
                            'msg_image' => $input['msg_image'],
                            'msg_title' => $input['msg_title'],
                            'msg_abstract' => $input['msg_abstract'],
                            'msg_content' => $input['msg_content'],
                            'msg_link' => $input['msg_link'],
                        );
                        $this->sys_model_message->addMessage($data);
                    }
                }
            }

            $this->session->data['success'] = '添加系统消息成功！';

            //加载管理员操作日志 model
            $this->load->library('sys_model/admin_log', true);
            $data = array(
                'admin_id' => $this->logic_admin->getId(),
                'admin_name' => $this->logic_admin->getadmin_name(),
                'log_description' => '添加系统消息：',
                'log_ip' => $this->request->ip_address(),
                'log_time' => date('Y-m-d H:i:s')
            );
            $this->sys_model_admin_log->addAdminLog($data);
            
            $filter = array();

            $this->load->controller('common/base/redirect', $this->url->link('user/message', $filter, true));
        }

        $this->assign('title', '系统消息加');
        $this->getForm();
    }

    /**
     * 消息详情
     */
    public function info() {
        $msg_id = $this->request->get('msg_id');
        $condition = array(
            'msg_id' => $msg_id
        );
        $info = $this->sys_model_message->getMessageInfo($condition);
        $info['msg_image_url'] = HTTP_IMAGE . $info['msg_image'];

        $condition =array(
            'user_id' => $info['user_id']
        );
        $user = $this->sys_model_user->getUserInfo($condition);
        $info['user_name'] = $user['mobile'];
        
        $this->assign('data', $info);
        $this->assign('return_action', $this->url->link('user/message'));

        $this->response->setOutput($this->load->view('user/message_info', $this->output));
    }

    private function getForm() {
        // 编辑时获取已有的数据
        $info = $this->request->post(array('msg_title', 'msg_image', 'mobiles', 'msg_abstract', 'msg_link', 'msg_content'));

        $info['msg_image_url'] = !empty($info['msg_image']) ? HTTP_IMAGE . $info['msg_image'] : '';

        $this->assign('data', $info);
        $this->assign('action', $this->cur_url);
        $this->assign('return_action', $this->url->link('user/message'));
        $this->assign('upload_action', $this->url->link('common/upload'));
        $this->assign('error', $this->error);

        $this->response->setOutput($this->load->view('user/message_form', $this->output));
    }

    /**
     * 验证表单数据
     * @return bool
     */
    private function validateForm() {
        print_r($this->request->post(NULL));
        $input = $this->request->post(array('msg_title', 'msg_image', 'mobiles', 'msg_abstract', 'msg_link', 'msg_content'));

        foreach ($input as $k => $v) {
            if (empty($v)) {
                $this->error[$k] = '请输入完整！';
            }
        }

        if ($this->error) {
            $this->error['warning'] = '警告: 存在错误，请检查！';
        }
        return !$this->error;
    }
}