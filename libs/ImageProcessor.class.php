<?php
namespace Air\Libs;

/**
 * example:
 * $filename = "example.jpg";
 * $new_filename = "new.jpg";
 * $processor = new ImageProcessor($filename);
 * $processor->rescale(4);
 * $processor->addSologan('C');
 * $processor->addWatermark(4);
 * $processor->save($new_filename);
 */
class ImageProcessor {
    private $img;
    private $width;
    private $height;

    public function __construct($filename) {
        if (file_exists($filename)) {
            $image_type = exif_imagetype($filename);
            if (in_array($image_type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP])) {
                $this->createImage($filename, $image_type);
            }
        }
    }

    // add solgan at the bottom center of image
    public function addSlogan($left = 'L', $bottom = 0) {
        if ($this->img) {
            $slogan_filename = $this->getFitSizeSlogan();
            $slogan_img = imagecreatefrompng($slogan_filename);
            $slogan_image_width = imagesx($slogan_img);
            $slogan_image_height = imagesy($slogan_img);
            if ($left == 'L' || $left == 'left') {
                $dst_x = 5;
            } else if ($left == 'R' || $left == 'right') {
                $dst_x = $this->width - $slogan_image_width - 5;
            } else {
                $dst_x = ($this->width - $slogan_image_width) / 2;
            }
            $dst_y = $this->height - $slogan_image_height - $bottom;
            imagecopy($this->img, $slogan_img, $dst_x, $dst_y, 0, 0, $slogan_image_width, $slogan_image_height);
            imagedestroy($slogan_img);
        }
    }

    public function addWaterMark($num = 1) {
        $watermark_filename = $this->getFitSizeWatermark();
        $watermark_img = imagecreatefrompng($watermark_filename);
        $watermark_width = imagesx($watermark_img);
        $watermark_height = imagesy($watermark_img);
        $row = $this->height / $watermark_height;
        $col = $this->width / $watermark_width;
        $total = $row * $col;
        $num = min($num, $total);
        $idxs = range(0, $total - 1);
        shuffle($idxs);
        for($i = 0; $i < $num; $i++) {
            $dst_x = ($idxs[$i] % $col) * $watermark_width;
            $dst_y = ($idxs[$i] / $col) * $watermark_height;
            imagecopy($this->img, $watermark_img, $dst_x, $dst_y, 0, 0, $watermark_width, $watermark_height);
        }
        imagedestroy($watermark_img);
    }

    /*
     * this method should be called before the method addSlogan/addWatermark
     */
    public function rescale($scale = 4) {
        $this->img = imagescale($this->img, $this->width / $scale, $this->height / $scale);
        $this->width = $this->width / $scale;
        $this->height = $this->height / $scale;
    }

    public function save($filename)  {
        if ($this->img) {
            return imagejpeg($this->img, $filename, 100);
        }

        return FALSE;
    }

    public function getSize() {
        if ($this->img) {
            return [imagesx($this->img), imagesy($this->img)];
        }

        return FALSE;
    }

    public function createImage($filename, $image_type) {
        if (file_exists($filename)) {
            switch($image_type) {
                case IMAGETYPE_GIF:
                    $this->img = imagecreatefromgif($filename);
                    break;
                case IMAGETYPE_PNG:
                    $this->img = imagecreatefrompng($filename);
                    break;
                case IMAGETYPE_JPEG:
                    $this->img = imagecreatefromjpeg($filename);
                    break;
                case IMAGETYPE_BMP:
                    $this->img = imagecreatefrombmp($filename);
                    break;
            }
        }
        if ($this->img) {
            $this->width = imagesx($this->img);
            $this->height = imagesy($this->img);
        }
    }

    private function getFitSizeSlogan() {
        if ($this->width >= 2000) {
            // xl.png
            return ROOT_PATH . '/config/assets/slogan/slogon2022L.png';
        } else if ($this->width >= 1024) {
            // l.png
            return ROOT_PATH . '/config/assets/slogan/slogon2022.png';
        } else if ($this->width >= 512) {
            // m.png
            return ROOT_PATH . '/config/assets/slogan/slogon2022s.png';
        } else {
            // s.png
            return ROOT_PATH . '/config/assets/slogan/slogon2022s.png';
        }
    }

    private function getFitSizeWatermark() {
        if ($this->width >= 2048) {
            return ROOT_PATH . '/config/assets/watermark/xl.png';
        } else if ($this->width >= 1024) {
            return ROOT_PATH . '/config/assets/watermark/l.png';
        } else if ($this->width >= 512) {
            return ROOT_PATH . '/config/assets/watermark/m.png';
        } else {
            return ROOT_PATH . '/config/assets/watermark/s.png';
        }
    }

    public function __destruct() {
        if ($this->img) {
            imagedestroy($this->img);
            $this->img = NULL;
        }
    }

    public function destroy() {
        if ($this->img) {
            imagedestroy($this->img);
            $this->img = NULL;
        }
    }
}
