<?php
class ShoppingCommandFile {
  private static $productSearchSyncFile = null;
  private static $productSearchSyncFileName = null;
  private static $portalSyncFileName = '';
  private static $portalSyncFile = null;

  public static function initialize($merchantId, $categoryName, $categoryId, $version) {
    self::$portalSyncFileName = DATA_PATH.'portal_sync/'.$merchantId.'_'.$categoryId.'_'.$version.'_portal';
    self::$portalSyncFile = fopen(self::$portalSyncFileName, 'a');
    self::$productSearchSyncFileName = DATA_PATH.'product_search_sync/'.$merchantId.'_'.$categoryId.'_'.$version.'_product_search';
    self::$productSearchSyncFile = fopen(self::$productSearchSyncFileName, 'a');
  }

  public static function insertCategory($id, $name) {
    self::outputForPortal("c\n".$id."\n".$name."\n");
  }

  public static function insertPropertyKey($id, $name) {
    self::outputForPortal("k\n".$id."\n".$name."\n");
  }

  public static function insertPropertyValue($id, $keyId, $name) {
    self::outputForPortal("v\n".$id."\n".$keyId."\n".$name."\n");
  }

  public static function insertProduct($product, $id) {
    $output = "p\n".$id."\n";
    $output .= $product['uri_argument_list']."\n";
    $output .= $product['image_path']."\n";
    $output .= $product['image_digest']."\n";
    $output .= $product['title']."\n";
    $output .= $product['price_from_x_100']."\n";
    $output .= $product['price_to_x_100']."\n";
    $output .= $product['category_name']."\n";
    $output .= $product['property_list']."\n\n";
    $output .= $product['agency_name'];
    $output .= $product['keyword_list']."\n";
    $output .= $product['value_id_list']."\n";
    self::outputForPortal($output."\n");
  }

  public static function updateProduct($product) {
    $output = "u\n".$product['id'];
    if (isset($product['uri_argument_list'])) {
      $output .= "\n0".$product['uri_argument_list'];
    }
    if (isset($product['image_digest'])) {
      $output .= "\n1".$product['image_digest'];
    }
    if (isset($product['title'])) {
      $output .= "\n2".$product['title']."\n";
    }
    if (isset($product['price_from_x_100'])) {
      $output .= "\n3".$product['price_from_x_100'];
    }
    if (isset($product['price_to_x_100'])) {
      $output .= "\n4".$product['price_to_x_100'];
    }
    if (isset($product['category_name'])) {
      $output .= "\n5".$product['category_name'];
    }
    if (isset($product['property_list'])) {
      $output .= "\n6".$product['property_list']."\n";
    }
    if (isset($product['agency_name'])) {
      $output .= "\n7".$product['agency_name'];
    }
    if (isset($product['value_id_list'])) {
      $output .= "\n3".$product['value_id_list'];
    }
    if (isset($product['keyword_list'])) {
      $output .= "\n4".$product['keyword_list']."\n";
    }
    self::outputForPortal($output."\n");
  }

  public static function deleteProduct($id) {
    self::outputForPortal("d\n".$id."\n");
  }

  private static function outputForPortal($content) {
    fwrite(self::$portalSyncFile, $content);
  }

  public static function finalize() {
    fclose(self::$portalSyncFile);
    //TODO: check file size if = 0 return
    if (filesize(self::$portalSyncFileName) !== 0) {
      system('gzip '.self::$portalSyncFileName);
    } else {
      unlink(self::$portalSyncFileName);
    }
  }
}