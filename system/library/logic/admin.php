<?php
namespace Logic;
class Admin {
    private $admin_id;
    private $admin_name;
    private $permission = array();
    private $data = array();

    /**
     * User constructor. 如果用户已经登陆，从数据库获取用户信息和权限
     * @param Registry $registry 系统注册表
     */
    public function __construct($registry) {
        $this->db = $registry->get('db');
        $this->request = $registry->get('request');
        $this->session = $registry->get('session');
        $this->admin = new \Sys_Model\Admin($registry);
        $this->rbac = new \Sys_Model\Rbac($registry);

        // 如果已经登录就获取用户信息
        if (isset($this->session->data['admin_id'])) {
            $condition = array(
                'admin_id' => $this->session->data['admin_id'],
                'state' => 1
            );
            $user = $this->admin->getAdminInfo($condition);
            if (!empty($user) && is_array($user)) {
                $this->admin_id = $user['admin_id'];
                $this->admin_name = $user['admin_name'];
                $this->data = $user;

                // 角色权限
                $condition = array(
                    'role_id' => $user['role_id']
                );
                $rolePermission = $this->rbac->getRolePermissionList($condition);
                $rolePermissionIds = array_unique(array_column($rolePermission, 'permission_id'));

                $condition = array(
                    'permission_id' => array('in', $rolePermissionIds)
                );
                $permissions = $this->rbac->getPermissionList($condition);
                $menu = array();
                if (!empty($permissions) && is_array($permissions)) {
                    foreach ($permissions as $permission) {
                        $this->permission[$permission['permission_action']] = $permission;
                        $menu[] = $permission['permission_menu_id'];
                    }
                }
                $this->data['menu'] = array_unique($menu);

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
    public function login($admin_name, $password) {
        $condition = array(
            'admin_name' => $admin_name
        );
        $result = $this->admin->getAdminInfo($condition);
        if (!$result) {
            return callback(false, 'error_user_nonexistence');
        }
        if (!$this->admin->checkPassword($password, $result)) {
            return callback(false, 'error_login_password');
        }
        // 更新登录信息
        $condition = array(
            'admin_id' => $result['admin_id']
        );
        $clientIp = $this->request->ip_address();
        $data = array(
            'login_time' => TIMESTAMP,
            'login_ip' => $clientIp
        );

        $rec = $this->admin->updateAdmin($condition, $data);
        if (!$rec) {
            return callback(false, 'error_update_user_info');
        }

        echo $this->session->data['admin_id'] = $result['admin_id'];
        return callback(true, 'success_login', $result);
    }

    /**
     * 登出
     */
    public function logout() {
        unset($this->session->data['admin_id']);

        $this->admin_id = '';
        $this->admin_name = '';
    }

     /**
     * 判断当前用户是否已经登陆
     * @return mixed 如果没有登陆，则返回null，否则返回当前用户的id
     */
    public function isLogged() {
        return $this->admin_id;
    }

    /**
     * 获取当前用户的id
     * @return mixed 如果没有登陆，则返回null，否则返回当前用户的id
     */
    public function getId() {
        return $this->admin_id;
    }

    /**
     * 获取当前用户的用户名
     * @return mixed 如果没有登陆，则返回null，否则返回当前用户的admin_name
     */
    public function getadmin_name() {
        return $this->admin_name;
    }

    /**
     * 当前用户是否有指定操作的权限
     * @param $action  string 操作（Controller路由）
     * @return bool 是否拥有权限
     */
    public function hasPermission($action) {
        return isset($this->permission[$action]);
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
        return $this->db->table('admin')->insert($data);
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
        $this->admin->updateAdmin($where, $data);
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