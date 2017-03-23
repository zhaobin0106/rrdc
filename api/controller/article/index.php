<?php
/**
 * 东莞市亦强软件有限公司
 * Author: 罗剑波
 * Time: 1/8/2017 9:51 AM
 */

class ControllerArticleIndex extends Controller {
    public function index() {

        $this->load->library('sys_model/article');
        $data = array();

        $condition = array();
        $order = 'article_sort ASC';
        $result = $this->sys_model_article->getArticleList($condition, $order);
        if (is_array($result) && !empty($result)) {
            foreach ($result as $val) {
                $data[] = array(
                    'id' => $val['article_id'],
                    'code' => $val['article_code'],
                    'link' => sprintf('%sarticle/zh/%s.html', HTTP_IMAGE, $val['article_code'])
                );
            }
        }

        $this->response->showSuccessResult($data);
    }
}