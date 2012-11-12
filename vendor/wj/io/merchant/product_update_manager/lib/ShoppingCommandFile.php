<?php
class ShoppingCommandFile {
  private static $list = array();

  public static function insertCategory($id, $name) {
    self::$list[] = "\nc\n".$id."\n".$name;
  }

  public static function insertPropertyKey($id, $name) {
    self::$list[] = "\nk\n".$id."\n".$name;
  }

  public static function insertPropertyValue($id, $keyId, $name) {
    self::$list[] = "\nv\n".$id."\n".$keyId."\n".$name;
  }

  public static function insertProduct($product, $id) {
    self::$list[] = "\nip\n".$id;
  }

  public static function insertProductSearch() {
  }

  public static function updateProduct() {
  }

  public static function deleteProduct($id) {
    self::$list[] = "\ndp\n".$id;
  }

  public static function updateProductSearch() {
  }

  public static function deleteProductSearch() {
  }

  public static function finalize() {
    //TODO:压缩并移动指令文件到 ftp 服务器
    system('zip');
  }
}