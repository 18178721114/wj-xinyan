<?php

namespace Air\Scripts;

use Air\Libs\ImgWater;

class Water 
{
    public function __construct($arg) {
		if (!empty($arg[0])) {
         	$this->dir = $arg[0];
		}
    }
    private $dir = '/Users/chenhailong/Documents/CDC/20190327/left';
    public function run()
    {
        $mydir = dir($this->dir);
         while($file = $mydir->read())
         {
             if ($file != "." && $file != ".." && strpos($file, '.jpg')) {	
                 $filename = $file;
                 $this->imgWater($filename);
             }
         }
         $mydir->close();

        //$url = $this->imgWater(['url' => 'http://img.airdoc.com/1550753937034_1528213727872_auto_topcon_820650_973E712C-35B2-488E-984F-33A552201B19_OS-.jpg']); 
    }

    private function imgWater($img)
    {
        $uinfo = pathinfo($img);
        $ext = 1;
        if (empty($uinfo['extension'])) {
            $ext = 0;
            $uinfo['extension'] = 'jpg';
        }
        $full_img = $this->dir . '/' . $img;
        $imgWater = new ImgWater($full_img);
        $img_size = getimagesize($full_img);
        $x = rand(bcmul($img_size[0], 0.3), bcmul($img_size[0], 0.7));
        $y = rand(bcmul($img_size[1], 0.05), bcmul($img_size[1], 0.15));
        $imgWater->imgmark(ROOT_PATH . '/scripts/img/Airdoc_logo.png', $x, $y, 2);
        $x = rand(bcmul($img_size[0], 0.3), bcmul($img_size[0], 0.7));
        $y = rand(bcmul($img_size[1], 0.85), bcmul($img_size[1], 0.95));
        $imgWater->imgmark(ROOT_PATH . '/scripts/img/Airdoc_logo.png', $x, $y, 2);
        $new_file = $uinfo['filename']; 
        $imgWater->save($this->dir . '-new/' . $new_file);
    }

}
