<?php

/**
 * Class ControllerCommonUpload
 * 文件上传
 */
class ControllerCommonUpload extends Controller {

    public function __construct($registry) {
        parent::__construct($registry);

        // 加载管理员 Model
        $this->load->library('sys_model/admin', true);
    }

    /**
     * 上传常规文件
     */
    public function index() {
        $tage = $this->request->post('tage');
        switch ($tage) {
            case 'app' :
                $filePath = 'app/';
                $fileName = date('YmdHis').token(3, 'number');
                $maxSize = 10 * 1024 * 1024;         // 2M
                $allowFiles = array('apk');
                break;
            case 'image' :
                $filePath = 'img/';
                $fileName = date('YmdHis') . token(3, 'number');
                $maxSize = 2 * 1024 * 1024;         // 2M
                $allowFiles = array('jpg', 'png', 'gif');
                break;
        }
        $config = array(
            "filePath" => $filePath,
            "fileName" => $fileName,
            "maxSize" => $maxSize,
            "allowFiles" => $allowFiles
        );
        // 文件变量名称
        $fileField = 'upfile';
        // 文件类型
        $type = 'upload';
        $up =  new Uploader($fileField, $config, $type, $this->request->files);
        $info = $up->getFileInfo();
        if ($info['state'] == 'SUCCESS') { // 上传成功
            $data = array();
            switch ($tage) {
                case 'app' :
                    $apkInfo = $this->get_apk_info(DIR_STATIC . $info['filePath']);
                    $data = array(
                        'filepath' => $info['filePath'],
                        'version_name' => $apkInfo['version_name'],
                        'version_code' => $apkInfo['version_code'],
                    );
                    break;
            }
            $this->response->showSuccessResult($data, '上传成功');
        } else { // 上传失败
            $this->response->showErrorResult($info['state']);
        }
    }

    /**
     * 获取apk文件信息
     * @param $targetFile
     */
    private function get_apk_info($targetFile) {
        $appObj  = new Apkparser();
        $data = array(
            'version_name' => null,
            'version_code' => null,
        );
        if ($appObj->open($targetFile)) {
            $data['version_name'] = $appObj->getVersionName();
            $data['version_code'] = $appObj->getVersionCode();
        }
        return $data;
    }
}