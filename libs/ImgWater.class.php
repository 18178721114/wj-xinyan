<?php
/**
 * 图片水印
 * Date: 2017/9/28
 * Time: 上午10:12
 */

namespace Air\Libs;


class ImgWater {
    private $imginfo;
    private $image;


    /**
     * 创建图片
     * @param string $src 图片地址
     */
    function __construct($src) {
        $imginfo = getimagesize($src);//返回一个数组，[0]图片width,[1]图片height,[2]图片类型...
        $this->imginfo = array(
            'width' => $imginfo[0],
            'height' => $imginfo[1],
            'type' => image_type_to_extension($imginfo[2], false),
            'mime' => $imginfo['mime']);
        if ($this->imginfo['type'] == 'bmp') {
            $this->imginfo['type'] = 'wbmp';
        }
        if (empty($this->imginfo['type']) || $this->imginfo['type'] == 'tiff') {
            $this->imginfo['type'] = 'jpeg';
        }
        $imgcreate = "imagecreatefrom{$this->imginfo['type']}";
        $this->image = @$imgcreate($src);
    }


    /**
     * 压缩图片
     * @param   $width  压缩后宽度
     * @param   $height 压缩后高度
     */
    public function thumb($width, $height) {
        $img_thumb = imagecreatetruecolor($width, $height);
        imagecopyresampled($img_thumb, $this->image, 0, 0, 0, 0, $width, $height, $this->imginfo['width'], $this->imginfo['height']);
        imagedestroy($this->image);
        $this->image = $img_thumb;
    }


    /**
     * 为图片添加文字水印
     * @param  string $content 文字内容
     * @param  string $font 字体文件地址
     * @param  integer $fontsize 字体大小
     * @param  integer $top 在图片上的x坐标
     * @param  integer $left y坐标
     */
    public function fontmark($content, $font = ROOT_PATH . '/libs/img/STZhongsong.ttf', $fontsize = 35, $top = 20, $left = 195) {
        $blue  =  imagecolorallocate ($this->image,  105 ,  158 ,  195 );
        $grey  =  imagecolorallocate ($this->image,  220 ,  220 ,  220 );  //表示阴影效果
        $white  =  imagecolorallocate ($this->image,  255 ,  255 ,  255 );
        $col_black = imagecolorallocatealpha($this->image, 220, 220, 220, 2);
        $text_count = strlen($content);
        for ($i =0; $i < $text_count; $i++) {
            $size = rand(12, 16);
            $ts = imagettfbbox( $size, 0, $font, $content[$i] );
            $x = abs($ts[2]-$ts[0]);
            if ($x < 13){ $x = 15;}
            $grad = range ( -45, 45 );
            shuffle ( $grad );
            imagettftext($this->image, $fontsize, $grad[1], $top, $left, $col_black, $font, $content[$i]);
            $top += ($x+2);
        }

    }


    /**
     * 为图片添加图片水印
     * @param  string $mark_src 水印图片地址
     * @param  integer $x 在源图片上的X坐标
     * @param  integer $y y坐标
     * @param  integer $ap 水印图片透明度
     */
    public function imgmark($mark_src, $x = 170, $y = 170, $ap = 3) {
        $imginfo2 = getimagesize($mark_src);
        $type2 = image_type_to_extension($imginfo2[2], false);
        $imgcreate = "imagecreatefrom{$type2}";
        $water = $imgcreate($mark_src);
        $angle = rand(-8, 8);
        $rotate = imagerotate($water, $angle, 3);
        imagecopymerge($this->image, $rotate, $x, $y, 0, 0, imagesx($water), imagesy($water), $ap);
        imagedestroy($water);
    }


    /**
     * 输出图片
     */
    public function show() {
        header('Content-type:' . $this->imginfo['mime']);
        $outimg = "image{$this->imginfo['type']}";
        $outimg($this->image);
    }


    /**
     * 保存图片
     * @param   $newname 保存图片名称
     */
    public function save($newname) {
        $saveimg = "image{$this->imginfo['type']}";
        if (strpos($newname, '.jpg')) {
            $saveimg($this->image, $newname);
        }
        else {
            $saveimg($this->image, $newname . '.' . $this->imginfo['type']);
        }
    }


    /**
     * 利用析构函数销毁内存中的图片
     */
    public function __destruct() {
        imagedestroy($this->image);
    }
}
