<?php
namespace Logic;
class cooperator {
    private $cooperator_id;
    private $cooperator_name;
    private $data = array();

    /**
     * User constructor. 如果用户已经登陆，从数据库获取用户信息和权限
     * @param Registry $registry 系统注册表
     */
    public function __construct($registry) {
        $this->db = $registry->get('db');
        $this->request = $registry->get('request');
        $this->session = $registry->get('session');
        $this->cooperator = new \Sys_Model\cooperator($registry);

        // 如果已经登录就获取用户信息
        if (isset($this->session->data['cooperator_id'])) {
            $condition = array(
                'cooperator_id' => $this->session->data['cooperator_id'],
                'state' => 1
            );
            $user = $this->cooperator->getcooperatorInfo($condition);
            if (!empty($user) && is_array($user)) {
                $this->cooperator_id = $user['cooperator_id'];
                $this->cooperator_name = $user['cooperator_name'];
                $this->data = $user;

                // TODO 权限验证

            } else {
                $this->logout();
            }
        }
    }

    /**
     * 用户登录
     * @param $mobile
     * @param $device_id
     * @return array
     */
    public function login($cooperator_name, $password) {
        $condition = array(
            'cooperator_name' => $cooperator_name
        );
        $result = $this->cooperator->getCooperatorInfo($condition);
        if (!$result) {
            return callback(false, 'error_user_nonexistence');
        }
        if (!$this->cooperator->checkPassword($password, $result)) {
            return callback(false, 'error_login_password！');
        }
        // 更新登录信息
        $condition = array(
            'cooperator_id' => $result['cooperator_id']
        );
        $clientIp = $this->request->ip_address();
        $data = array(
            'login_time' => TIMESTAMP,
            'login_ip' => $clientIp
        );

        $rec = $this->cooperator->updateCooperator($condition, $data);
        if (!$rec) {
            return callback(false, 'error_update_user_info');
        }

        echo $this->session->data['cooperator_id'] = $result['cooperator_id'];
        return callback(true, 'success_login', $result);
    }

    /**
     * 登出
     */
    public function logout() {
        unset($this->session->data['cooperator_id']);

        $this->cooperator_id = '';
        $this->cooperator_name = '';
    }

     /**
     * 判断当前用户是否已经登陆
     * @return mixed 如果没有登陆，则返回null，否则返回当前用户的id
     */
    public function isLogged() {
        return $this->cooperator_id;
    }

    /**
     * 获取当前用户的id
     * @return mixed 如果没有登陆，则返回null，否则返回当前用户的id
     */
    public function getId() {
        return $this->cooperator_id;
    }

    /**
     * 获取当前用户的用户名
     * @return mixed 如果没有登陆，则返回null，否则返回当前用户的cooperator_name
     */
    public function getcooperator_name() {
        return $this->cooperator_name;
    }

    /**
     * 获取当前用户的用户名
     * @return mixed 如果没有登陆，则返回null，否则返回当前用户的cooperator_name
     */
    public function getFullName() {
        return $this->fullname;
    }

    /**
     * 获取当前用户的角色id
     * @return mixed 如果没有登陆，则返回null，否则返回当日按用户的角色id
     */
    public function getRoleId() {
        return $this->role_id;
    }

    /**
     * 获取当前用户的类型
     * @return string 'cooperator' or 'agent' or 'seller'
     */
    public function getType() {
        return $this->type;
    }

    /**
     * 获取当前用户的归属id
     * @return int 如果没有归属id 就返回自身
     */
    public function getBelong() {
        return $this->belong;
    }

    /**
     * 获取用户信息参数
     * @param $key
     * @return mixed|null
     */
    public function getParam($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * 获取用户账号资料
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * 添加管理员
     * @param $data
     * @return mixed
     */
    public function add($data) {
        if (empty($data)) {
            return false;
        }
        if (isset($data['password'])) {
            $data['salt'] = token(10);
            $data['password'] = sha1($data['salt'] . sha1($data['salt'] . sha1($data['password'])));
        }
        return $this->db->table('cooperator')->insert($data);
    }

    /**
     * 更新用户信息
     * @param $where
     * @param $data
     * @return mixed
     */
    public function update($where, $data) {
        if (empty($data)) {
            return false;
        }
        if (isset($data['password'])) {
            $data['salt'] = token(10);
            $data['password'] = sha1($data['salt'] . sha1($data['salt'] . sha1($data['password'])));
        }
        $this->cooperator->updatecooperator($where, $data);
    }


    // -------------------------------------- 其他 --------------------------------------
    /**
     * 检验 会员密码长度
     * @param $password
     * @return bool
     */
    public function checkPasswordFormat($password) {
        return preg_match("/^[a-zA-Z\d_]{6,}$/", $password) ? true : false;
    }
}