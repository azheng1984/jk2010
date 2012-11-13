<?php
class ShoppingCommandFile {
  private static $productSearchSyncFile = null;
  private static $portalSyncFile = null;

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
    $output = "\nip\n".$id."\n";
    $output .= $product['merchant_id']."\n";
    $output .= "\n";
    $output .= "\n";
    $output .= "\n";
  }

  public static function updateProduct() {
  }

  public static function deleteProduct($id) {
    self::$list[] = "\ndp\n".$id;
  }

  public static function insertProductSearch() {
  }

  public static function updateProductSearch() {
  }

  public static function deleteProductSearch() {
  }

  private static function outputForPortal($line) {
    if (self::$portalSyncFile === null) {
      self::$portalSyncFile = fopen('', 'w+');
    }
  }

  private static function outputForProductSearch($line) {
    if (self::$productSearchSyncFile === null) {
      self::$productSearchSyncFile = fopen('', 'w+');
    }
  }

  public static function finalize() {
    //TODO:压缩并移动指令文件到 ftp 服务器
    system('zip');
  }
}