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
    self::outputForPortal($output."\n");
  }

  public static function deleteProduct($id) {
    self::outputForPortal("d\n".$id."\n");
  }

  public static function insertProductSearch($priceFromX100, $id, $categoryId, $valueIdTextList, $keywordTextList) {
    $output = "p\n".$id."\n";
    $output .= $categoryId."\n";
    $output .= $priceFromX100."\n";
    $output .= $valueIdTextList."\n";
    $output .= $keywordTextList."\n";
    self::outputForPortal($output);
  }

  public static function updateProductSearch($product) {
    $output = "u\n".$product['id'];
    if (isset($product['category_id'])) {
      $output .= "\n1".$product['category_id'];
    }
    if (isset($product['price_from_x_100'])) {
      $output .= "\n2".$product['price_from_x_100'];
    }
    if (isset($product['value_id_list'])) {
      $output .= "\n3".$product['value_id_list'];
    }
    if (isset($product['keyword_list'])) {
      $output .= "\n4".$product['keyword_list']."\n";
    }
    self::outputForPortal($output);
  }

  public static function deleteProductSearch($id) {
    self::outputForPortal("d\n".$id."\n");
  }

  private static function outputForPortal($content) {
    fwrite(self::$portalSyncFile, $content);
  }

  private static function outputForProductSearch($content) {
    fwrite(self::$productSearchSyncFile, $content);
  }

  public static function finalize() {
    fclose(self::$portalSyncFile);
    //check file size
    $fileList = array();
    if (filesize(self::$portalSyncFileName) !== 0) {
      $fileList['portal'] = true;
      system('gzip '.self::$portalSyncFileName);
    } else {
      unlink(self::$portalSyncFileName);
    }
    fclose(self::$productSearchSyncFile);
    if (filesize(self::$productSearchSyncFileName) !== 0) {
      $fileList['product_search'] = true;
      system('gzip '.self::$productSearchSyncFileName);
    } else {
      unlink(self::$productSearchSyncFileName);
    }
    return $fileList;
  }
}