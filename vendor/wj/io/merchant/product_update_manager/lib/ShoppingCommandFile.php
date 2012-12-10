<?php
class ShoppingCommandFile {
  private static $portalSyncFileName = '';
  private static $portalSyncFile = null;
  private static $previousCommand = null;

  public static function initialize(
    $taskId, $merchantId, $categoryId, $version
  ) {
    self::$portalSyncFileName = DATA_PATH.'portal_sync/'
      .$taskId.'_'.$merchantId.'_'.$categoryId.'_'.$version;
    self::$portalSyncFile = fopen(self::$portalSyncFileName, 'w');
  }

  public static function insertCategory($id, $name) {
    $command = '';
    if (self::$previousCommand !== 'c') {
      self::$previousCommand = 'c';
      $command .= "c\n";
    }
    self::outputForPortal($command.$id."\n".$name."\n");
  }

  public static function insertPropertyKey($id, $name) {
    $command = '';
    if (self::$previousCommand !== 'k') {
      self::$previousCommand = 'k';
      $command .= "k\n";
    }
    self::outputForPortal($command.$id."\n".$name."\n");
  }

  public static function insertPropertyValue($id, $keyId, $name) {
    $command = '';
    if (self::$previousCommand !== 'v') {
      self::$previousCommand = 'v';
      $command .= "v\n";
    }
    self::outputForPortal($command.$id."\n".$keyId."\n".$name."\n");
  }

  public static function insertProduct($product, $id) {
    $output = '';
    if (self::$previousCommand !== 'p') {
      self::$previousCommand = 'p';
      $output = "p\n";
    }
    $output .= $id."\n";
    $output .= $product['uri_argument_list']."\n";
    $output .= $product['image_path']."\n";
    $output .= $product['image_digest']."\n";
    $output .= $product['title']."\n";
    $output .= $product['price_from_x_100']."\n";
    $output .= $product['price_to_x_100']."\n";
    $output .= $product['category_name']."\n";
    $output .= $product['property_list']."\n\n";
    $output .= $product['agency_name']."\n";
    $output .= $product['keyword_list']."\n";
    $output .= $product['value_id_list']."\n";
    self::outputForPortal($output);
  }

  public static function updateProduct($id, $replacementColumnList) {
    $output = '';
    if (self::$previousCommand !== 'u') {
      self::$previousCommand = 'u';
      $output = "u\n";
    }
    $output = $id;
    if (isset($replacementColumnList['uri_argument_list'])) {
      $output .= "\n0".$replacementColumnList['uri_argument_list'];
    }
    if (isset($replacementColumnList['image_digest'])) {
      $output .= "\n1".$replacementColumnList['image_digest'];
    }
    if (isset($replacementColumnList['title'])) {
      $output .= "\n2".$replacementColumnList['title'];
    }
    if (isset($replacementColumnList['price_from_x_100'])) {
      $output .= "\n3".$replacementColumnList['price_from_x_100'];
    }
    if (isset($replacementColumnList['price_to_x_100'])) {
      $output .= "\n4".$replacementColumnList['price_to_x_100'];
    }
    if (isset($replacementColumnList['category_name'])) {
      $output .= "\n5".$replacementColumnList['category_name'];
    }
    if (isset($replacementColumnList['property_list'])) {
      $output .= "\n6".$replacementColumnList['property_list']."\n";
    }
    if (isset($replacementColumnList['agency_name'])) {
      $output .= "\n7".$replacementColumnList['agency_name'];
    }
    if (isset($replacementColumnList['value_id_list'])) {
      $output .= "\n8".$replacementColumnList['value_id_list'];
    }
    if (isset($replacementColumnList['keyword_list'])) {
      $output .= "\n9".$replacementColumnList['keyword_list'];
    }
    self::outputForPortal($output."\n\n");
  }

  public static function deleteProduct($id) {
    $command = '';
    if (self::$previousCommand !== 'd') {
      self::$previousCommand = 'd';
      $command = "d\n";
    }
    self::outputForPortal($command.$id."\n");
  }

  private static function outputForPortal($content) {
    fwrite(self::$portalSyncFile, $content);
  }

  public static function finalize() {
    fclose(self::$portalSyncFile);
    if (filesize(self::$portalSyncFileName) !== 0) {
      system('gzip '.self::$portalSyncFileName);
    } else {
      unlink(self::$portalSyncFileName);
    }
  }

  public static function clean() {
    
  }
}