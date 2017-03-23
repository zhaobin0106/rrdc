<?php

class ControllerCommonMenu extends Controller {

    public function index() {

        $data = array(
            array(
                'action' => 'bicycle/bicycle',
                'icon' => 'fa-bicycle',
                'text_code' => '单车管理',
            ),
            array(
                'action' => 'lock/lock',
                'icon' => 'fa-unlock-alt',
                'text_code' => '车锁管理',
            ),
            array(
                'action' => '',
                'icon' => 'fa-share-alt',
                'text_code' => '运维管理',
                'children' => array(
                    array(
                        'action' => 'operation/fault',
                        'icon' => 'fa-circle-o',
                        'text_code' => '故障管理',
                    ),
                    array(
                        'action' => 'operation/violation',
                        'icon' => 'fa-circle-o',
                        'text_code' => '违规停放',
                    ),
                    array(
                        'action' => 'operation/feedback',
                        'icon' => 'fa-circle-o',
                        'text_code' => '客户反馈',
                    ),
                )
            ),
            array(
                'action' => '',
                'icon' => 'fa-cog',
                'text_code' => '系统设置',
                'children' => array(
                    array(
                        'action' => '',
                        'icon' => 'fa-circle-o',
                        'text_code' => '管理员管理',
                        'children' => array(
                            array(
                                'action' => 'system/admin',
                                'icon' => 'fa-circle-o',
                                'text_code' => '管理员',
                            ),
                            array(
                                'action' => 'system/role',
                                'icon' => 'fa-circle-o',
                                'text_code' => '角色权限',
                            ),
                        )
                    ),
                    array(
                        'action' => 'system/log',
                        'icon' => 'fa-circle-o',
                        'text_code' => '操作日志',
                    )
                )
            ),
            array(
                'action' => 'me/information',
                'icon' => 'fa-shield',
                'text_code' => '个人中心',
            )
        );

        return $this->buildMenuTree($data, true);
    }

    /**
     * 把树形结构的数组转换成<ul>-<li>的树结构标签
     * @param array $tree 树结构的数组数据，每个节点包含下标：menu_id，text_code，action，parameter，icon和children。注意：如果节点是页节点，那么
     * @param boolean $is_root 是否为根节点
     * @return string
     */
    private function buildMenuTree($tree, $is_root = false) {
        $ul = '';
        if( !empty($tree) ) {
            $ul .= $is_root ? '<ul class="sidebar-menu">' : '<ul class="treeview-menu">';
            foreach ($tree as $menu) {
                $ul .= '<li>';
                $ul .= !empty($menu['children']) ? '<a href="javascript:;">' : '';
                $ul .= !empty($menu['action']) ? '<a href="' . $this->url->link($menu['action'], '', true) . '">' : '';
                $ul .= !empty($menu['icon']) ? '<i class="fa ' . $menu['icon'] . ' fa-fw"></i> <span>' : '';
                $ul .= $this->language->get($menu['text_code']);
                $ul .= !empty($menu['icon']) ? '</span>' : '';
                $ul .= !empty($menu['action']) ? '</a>' : '';
                $ul .= !empty($menu['children']) ? '<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>' : '';
                $ul .= !empty($menu['children']) ? $this->buildMenuTree( $menu['children'] ) : '';
                $ul .= '</li>';
            }
            $ul .= '</ul>';
        }
        return $ul;
    }

}
