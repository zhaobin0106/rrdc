<?php

/**
 * Class ControllerCommonUpload
 * 文件上传
 */
class ControllerCommonQrcode extends Controller {

    public function __construct($registry) {
        parent::__construct($registry);
        library('phpqrcode\phpqrcode');
    }

    /**
     * 生成纯二维码
     * @param array $data
     */
    public function buildQrCode($data = array()) {
        $saveFile = DIR_STATIC . 'images/qrcode/' . $data['code'] . '.png';
        QRcode::png($data['qrcodeText'], $saveFile, QR_ECLEVEL_H, 5, 1);
    }

    /**
     * 生成车前二维码图片
     */
    public function buildFrontQrCode($data = array()) {
        $text =  '编号：' . $data['code'];
//        $font = './simhei.ttf';
        $font = DIR_STATIC . 'AdminLTE-2.3.7/dist/fonts/simhei.ttf';
        $background = DIR_STATIC . 'images/qrcode/front.jpg';
        $saveFile = DIR_STATIC . 'images/qrcode/front_' . $data['code'] . '.png';
        $outerFrame = 1;
        $pixelPerPoint = 7.5;

        // generating frame
        $frame = QRcode::text($data['qrcodeText'], false, QR_ECLEVEL_H);

        // rendering frame with GD2 (that should be function by real impl.!!!)
        $h = count($frame);
        $w = strlen($frame[0]);

        $imgW = $w + 2 * $outerFrame;
        $imgH = $h + 2 * $outerFrame;

        $base_image = imagecreate($imgW, $imgH);

        $col[0] = imagecolorallocate($base_image, 255, 255, 255); // BG, white
        $col[1] = imagecolorallocate($base_image, 0, 0, 0);     // FG, blue

        imagefill($base_image, 0, 0, $col[0]);

        // 填色
        for($y=0; $y<$h; $y++) {
            for($x = 0; $x < $w; $x++) {
                if ($frame[$y][$x] == '1') {
                    imagesetpixel($base_image, $x+$outerFrame, $y+$outerFrame, $col[1]);
                }
            }
        }

        $target_image = imagecreate($imgW * $pixelPerPoint, $imgH * $pixelPerPoint + 45);
        imagecopyresized(
            $target_image,
            $base_image,
            0, 0, 0, 0,
            $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $imgW, $imgH
        );

        // 文字
        imagettftext($target_image, 35, 0, 18, $imgH * $pixelPerPoint + 28 + 10, $col[1], $font, $text);

        // 背景图
        $back = imagecreatefromjpeg($background);
        imagecopymerge($back, $target_image, 77, 95, 0, 0, imagesx($target_image), imagesy($target_image), 100);

        imagedestroy($base_image);
        imagepng($back, $saveFile);
        imagedestroy($target_image);

        // 更改图片 DPI
        $this->changeDpi($saveFile);
    }

    /**
     * 生成车后二维码图片
     */
    public function buildBackQrCode($data = array()) {
        $text =  '编号：' . $data['code'];
//        $font = './simhei.ttf';
        $font = DIR_STATIC . 'AdminLTE-2.3.7/dist/fonts/simhei.ttf';
        $background = DIR_STATIC . 'images/qrcode/back.jpg';
        $saveFile = DIR_STATIC . 'images/qrcode/back_' . $data['code'] . '.png';
        $outerFrame = 2;
        $pixelPerPoint = 4;

        // generating frame
        $frame = QRcode::text($data['qrcodeText'], false, QR_ECLEVEL_H);

        // rendering frame with GD2 (that should be function by real impl.!!!)
        $h = count($frame);
        $w = strlen($frame[0]);

        $imgW = $w + 2 * $outerFrame;
        $imgH = $h + 2 * $outerFrame;

        $base_image = imagecreate($imgW, $imgH);

        $col[0] = imagecolorallocate($base_image, 255, 255, 255); // BG, white
        $col[1] = imagecolorallocate($base_image, 0, 0, 0);     // FG, blue

        imagefill($base_image, 0, 0, $col[0]);

        // 填色
        for($y=0; $y<$h; $y++) {
            for($x = 0; $x < $w; $x++) {
                if ($frame[$y][$x] == '1') {
                    imagesetpixel($base_image, $x+$outerFrame, $y+$outerFrame, $col[1]);
                }
            }
        }

        $target_image = imagecreate($imgW * $pixelPerPoint + 12, $imgH * $pixelPerPoint + 30);
        imagecopyresized(
            $target_image,
            $base_image,
            0, 0, -1, 0,
            $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $imgW, $imgH
        );

        // 文字
        imagettftext($target_image, 18, 0, 16, $imgH * $pixelPerPoint + 22, $col[1], $font, $text);

        // 背景图
        $back = imagecreatefromjpeg($background);
        imagecopymerge($back, $target_image, 36, 24, 0, 0, imagesx($target_image), imagesy($target_image), 100);

        imagedestroy($base_image);
        imagepng($back, $saveFile);
        imagedestroy($target_image);

        // 更改图片 DPI
        $this->changeDpi($saveFile);
    }

    /**
     * 改变图片DPI分辨率
     * @param $saveFile
     */
    public function changeDpi($saveFile) {
        // 更改图片 DPI
        $file = file_get_contents($saveFile);
        //数据块长度为9
        $len = pack("N", 9);
        //数据块类型标志为pHYs
        $sign = pack("A*", "pHYs");
        //X方向和Y方向的分辨率均为300DPI（1像素/英寸=39.37像素/米），单位为米（0为未知，1为米）
        $data = pack("NNC", 300 * 39.37, 300 * 39.37, 0x01);
        //CRC检验码由数据块符号和数据域计算得到
        $checksum = pack("N", crc32($sign . $data));
        $phys = $len . $sign . $data . $checksum;

        $pos = strpos($file, "pHYs");
        if ($pos > 0) {
            //修改pHYs数据块
            $file = substr_replace($file, $phys, $pos - 4, 21);
        } else {
            //IHDR结束位置（PNG头固定长度为8，IHDR固定长度为25）
            $pos = 33;
            //将pHYs数据块插入到IHDR之后
            $file = substr_replace($file, $phys, $pos, 0);
        }
        file_put_contents($saveFile, $file);
    }
}