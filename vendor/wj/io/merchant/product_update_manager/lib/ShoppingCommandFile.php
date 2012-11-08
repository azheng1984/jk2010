<?php
class ShoppingCommandFile {
  private static $commandList = array();

  public static function insertCategory($id, $name) {
    self::$commandList[] = "\nc\n".$id."\n".$name;
  }

  public static function insertPropertyKey($id, $name) {
    self::$commandList[] = "\nk\n".$id."\n".$name;
  }

  public static function insertPropertyValue($id, $keyId, $name) {
    self::$commandList[] = "\nv\n".$id."\n".$keyId."\n".$name;
  }

  public static function deleteProduct($id) {
    self::$commandList[] = "\ndp\n".$id;
  }

  public static function finalize() {
    //TODO:压缩并移动指令文件到 ftp 服务器
    system('zip');
  }
}