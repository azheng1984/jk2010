<?php
class ShoppingImageFolder {
  public static function finalize() {
    //TODO:压缩 & 移动图片文件夹到 ftp 服务器
    system('zip');
  }
}