# VideoScreenshot
video.php，自动读取所设定目录下所有视频文件，并批量生成第一帧截图，并压缩图片等方法封装。
在平常做视频处理业务过程中封装的一些好用的方法，文件中每个方法都有做说明，简单易懂，现在共享出来方便大家使用

需要用到的方法：

/**
* desription 读取目录下所有文件
* @param sting $dir 目录路径
*/

function read_all($dir)

/**
* desription 获得视频文件的缩略图，默认截取第一秒第一帧
* @param sting $file 视频路径
* @param int $time 第几帧（默认为第一帧：1）
*/
getVideoCover()

//创建图片名称
get_image_mp4_path()

//判断文件夹是否存在，不存在则创建文件夹
video_mkdir()

/**
* desription 压缩图片
* @param sting $imgsrc 图片路径
* @param string $imgdst 压缩后保存路径
*/
compressed_image($imgsrc,$imgdst)

/**
 * desription 判断是否gif动画
 * @param sting $image_file图片路径
 * @return boolean t 是 f 否
 */
check_gifcartoon($image_file)
