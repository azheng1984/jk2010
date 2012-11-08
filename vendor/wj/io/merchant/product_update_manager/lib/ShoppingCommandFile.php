<?php
class ShoppingCommandFile {
  private static $commandList = array();

  public static function insertCategory($id, $name) {
    self::$commandList[] = "c\n".$id."\n".$name;
  }

  public static function finalize() {
    //TODO:压缩并移动指令文件到 ftp 服务器
    system('zip');
  }
}