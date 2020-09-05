<?php
ini_set('memory_limit','3072M');//临时设置最大内存占用为3G
set_time_limit(0);

class Video {
  public function __construct() {}

  /**
   * 开始遍历目录下的视频
   *
   * @access public
   * @param  id 产品ID
   * @param  titles 产品标题
   * @param  grade 二维码背景图
   * @return string
   */
  public function run($file_path) {
    $file_path = self::read_all("/www/wwwroot/Test/video");
    for ($i = 0; $i < count($file_path); $i++) {
        //$str = str_replace("/www/wwwroot/Test/", "video_picture/", $file_path[$i]);
        //$str = str_replace('\\', "/", $str);
        self::getVideoCover($file_path[$i]);
    }
    return 'success';
  }

  /**
   * 获得视频文件的缩略图
   *
   * @access public
   * @param  file 视频路径
   * @param  time 截取位置，默认第一秒
   * @return string
   */
  private function getVideoCover($file, $time = 1) {
      //生成封面图，UPLOAD_PATH绝对路径
      $path = self::get_image_mp4_path();
      $str = 'ffmpeg -i '.$file.' -y -f mjpeg -ss 5 -t 1 -vf scale=iw*1:ih*1 '.$path['img_file'];
      echo $str;die;
      $a = system($str, $status);
      if($status == 0){
          //压缩
          self::compressed_image($path['img_file'], $path['img_file']);
          //视频重命名
          rename($file, $path['mp4_file']);
      }
      return;
  }

  /**
   * 创建图片名称
   *
   * @access public
   * @return array
   */
  public function get_image_mp4_path(){
      $name = md5(date('YmdHis', time().rand(1000, 9999)));
      $res['img_file'] = self::video_mkdir().'/'.$name.".jpg";
      $res['mp4_file'] = self::video_mkdir('video').'/'.$name.".mp4";
      return $res;
  }

  /**
   * 判断目录是否存在，不存在则创建
   *
   * @access public
   * @param  name 目录名
   * @return string
   */
  public function video_mkdir($name = 'video_picture'){
      $path = '/www/wwwroot/Test/'.$name.'/';
      if (!file_exists($path)){
          mkdir($path);
      }
      $path .= date("Ymd");
      if (!file_exists($path)){
          mkdir($path);
      }
      return '/www/wwwroot/Test/'.$name.'/'.date("Ymd");
  }

  /**
   * 读取目录下所有文件
   *
   * @access public
   * @param  name 目录名
   * @return string
   */
  public function read_all($dir){
      $file_arr = [];
      if(!is_dir($dir)) return false;
      $handle = opendir($dir);
      if($handle){
          while(($fl = readdir($handle)) !== false){
              $temp = $dir.DIRECTORY_SEPARATOR.$fl;
              //如果不加  $fl!='.' && $fl != '..'  则会造成把$dir的父级目录也读取出来
              if(is_dir($temp) && $fl!='.' && $fl != '..'){
                 /*//递归，目录内包含文件的情况
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
  public function check_gifcartoon($image_file){
    $fp = fopen($image_file,'rb');
    $image_head = fread($fp,1024);
    fclose($fp);
    return preg_match("/".chr(0x21).chr(0xff).chr(0x0b).'NETSCAPE2.0'."/",$image_head) ? false : true;
  }
 
  /**
  * desription 压缩图片
  * @param sting $imgsrc 图片路径
  * @param string $imgdst 压缩后保存路径
  */
  public function compressed_image($imgsrc,$imgdst){
    list($width,$height,$type) = getimagesize($imgsrc);
    /*$new_width = ($width>600? $width*0.5 : $width);
    $new_height = ($height>600 ? $height*0.5 : $height);*/
    $new_width = $width;
    $new_height = $height;
    switch($type){
      case 1:
        $giftype = self::check_gifcartoon($imgsrc);
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
}
/************************Test**********************************/
$obj = new Video();
$res = $obj->run("/www/wwwroot/Test/video_img");
var_dump($res);die;
/************************Test End**********************************/
