<?php
/*$img = 'D:\video_img\video_picture\aaa.jpg';
$str = 'ffmpeg -i D:\video_img\video\20180810\V55555.mp4 -y -f mjpeg -ss 5 -t 1 -vf scale=iw*1:ih*1 '.$img;
system($str, $status);
if($status == 0){
    compressed_image($img, $img);
}
die;*/
ini_set('memory_limit','3072M');    // 临时设置最大内存占用为3G
set_time_limit(0);

$file = read_all("D:/video_img/video/20180810");
for ($i=0; $i < count($file); $i++) {
    //echo $file[$i].'<br>';
    $str = str_replace("D:/video_img/video/", "video_picture/", $file[$i]);
    $str = str_replace('\\', "/", $str);
    echo $str.'<br>';
    //getVideoCover($file[$i]);
}
die;
//获得视频文件的缩略图
//默认截取第一秒第一帧
function getVideoCover($file, $time = 1) {
    //生成封面图，UPLOAD_PATH绝对路径
    $path = get_image_mp4_path();
    $str = 'ffmpeg -i '.$file.' -y -f mjpeg -ss 5 -t 1 -vf scale=iw*1:ih*1 '.$path['img_file'];
    system($str, $status);
    if($status == 0){
        //压缩
        compressed_image($path['img_file'], $path['img_file']);
        //视频重命名
        rename($file, $path['mp4_file']);
        //echo 1122;die;
        /*$res['status'] = 'success';
        $res['image_path'] = $image_path;*/
    }
    //return $res;
}

//创建图片名称
function get_image_mp4_path(){
    $name = md5(date('YmdHis', time().rand(1000, 9999)));
    $res['img_file'] = video_mkdir().'/'.$name.".jpg";
    $res['mp4_file'] = video_mkdir('video').'/'.$name.".mp4";
    return $res;
}

//判断文件夹是否存在，不存在则创建文件夹
function video_mkdir($name = 'video_picture'){
    $path = 'D:\video_img\/'.$name.'/';
    if (!file_exists($path)){
        mkdir($path);
    }
    $path .= date("Ymd");
    if (!file_exists($path)){
        mkdir($path); 
    }
    return 'D:/video_img/'.$name.'/'.date("Ymd");
}
//读取文件夹下所有文件
function read_all ($dir){
    $file_arr = [];
    if(!is_dir($dir)) return false;
    $handle = opendir($dir);
    if($handle){
        while(($fl = readdir($handle)) !== false){
            $temp = $dir.DIRECTORY_SEPARATOR.$fl;
            //如果不加  $fl!='.' && $fl != '..'  则会造成把$dir的父级目录也读取出来
            if(is_dir($temp) && $fl!='.' && $fl != '..'){
               /*//递归，文件夹内包含文件的情况
               echo '目录：'.$temp.'<br>';
               read_all($temp);
               */
            }else{
                if($fl!='.' && $fl != '..'){
                    $file_arr[] = $temp;
                }
            }
        }
   }
   return $file_arr;
}

/**
   * desription 判断是否gif动画
   * @param sting $image_file图片路径
   * @return boolean t 是 f 否
   */
  function check_gifcartoon($image_file){
    $fp = fopen($image_file,'rb');
    $image_head = fread($fp,1024);
    fclose($fp);
    return preg_match("/".chr(0x21).chr(0xff).chr(0x0b).'NETSCAPE2.0'."/",$image_head)?false:true;
  }
 
  /**
  * desription 压缩图片
  * @param sting $imgsrc 图片路径
  * @param string $imgdst 压缩后保存路径
  */
  function compressed_image($imgsrc,$imgdst){
    list($width,$height,$type)=getimagesize($imgsrc);
    /*$new_width = ($width>600? $width*0.5 : $width);
    $new_height = ($height>600 ? $height*0.5 : $height);*/
    $new_width = $width;
    $new_height = $height;
    switch($type){
      case 1:
        $giftype=check_gifcartoon($imgsrc);
        if($giftype){
          header('Content-Type:image/gif');
          $image_wp=imagecreatetruecolor($new_width, $new_height);
          $image = imagecreatefromgif($imgsrc);
          imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
          //75代表的是质量、压缩图片容量大小
          imagejpeg($image_wp, $imgdst,75);
          imagedestroy($image_wp);
        }
        break;
      case 2:
        header('Content-Type:image/jpeg');
        $image_wp=imagecreatetruecolor($new_width, $new_height);
        $image = imagecreatefromjpeg($imgsrc);
        imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        //75代表的是质量、压缩图片容量大小
        imagejpeg($image_wp, $imgdst,75);
        imagedestroy($image_wp);
        break;
      case 3:
        header('Content-Type:image/png');
        $image_wp=imagecreatetruecolor($new_width, $new_height);
        $image = imagecreatefrompng($imgsrc);
        imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        //75代表的是质量、压缩图片容量大小
        imagejpeg($image_wp, $imgdst,75);
        imagedestroy($image_wp);
        break;
    }
  }