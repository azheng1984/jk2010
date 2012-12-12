<?php
class SyncProduct {
  private static $version;
  private static $categoryId;
  private static $shoppingCategoryId;
  private static $merchantId;
  private static $merchantName;
  private static $propertyList;

  public static function execute(
    $categoryName, $shoppingCategoryId, $propertyList, $version, $merchantName
  ) {
    self::$merchantId = 1;//TODO
    DbConnection::connect($merchantName);
    self::$categoryId = Db::getColumn(
      'SELECT id FROM category WHERE name = ?', $categoryName
    );
    self::$version = $version;
    self::$merchantName = $merchantName;
    self::$shoppingCategoryId = $shoppingCategoryId;
    self::$propertyList = $propertyList;
    $productList = Db::getAll(
      'SELECT * FROM product WHERE category_id = ? ORDER BY id LIMIT 1000',
      self::$categoryId
    );
    self::sync($productList);
    while (count($productList) === 1000) {
      $product = end($productList);
      $productList = Db::getAll(
        'SELECT * FROM product WHERE category_id = ? AND id > ?'
          .' ORDER BY id LIMIT 1000',
        self::$categoryId, $product['id']
      );
      self::sync($productList);
    }
    DbConnection::close();
  }

  private function sync($productList) {
    foreach ($productList as $product) {
      $shoppingProduct = Db::get(
        'SELECT id, image_path FROM product'
          .' WHERE merchant_id = ? AND merchant_product_id = ?',
        self::$merchantId,
        $product['merchant_product_id']
      );
      if ($product['version'] < self::$version) {
        CommandSyncFile::deleteProduct($shoppingProduct['id']);
        SyncImage::delete($shoppingProduct['image_path']);
        Db::delete('product', 'id = ?', 1, $shoppingProduct['id']);
        continue;
      }
      DbConnection::connect(self::$merchantName);
      //product 可能会被分到不属于自身 category 的 property value
      $valueList = Db::getAll(
        'SELECT * FROM product_property_value'
          .' WHERE merchant_product_id = ? AND category_id = ?',
        $product['merchant_product_id'], self::$categoryId
      );
      DbConnection::close();
      $productPropertyList = array();
      $shoppingValueIdList = array();
      foreach ($valueList as $value) {
        $value = self::$propertyList['value_list'][$value['property_value_id']];
        $key = self::$propertyList['key_list'][$value['key_id']];
        if (isset($productPropertyList[$key['_index']]) === false) {
          $productPropertyList[$key['_index']] = array(
            'name' => $key['name'], 'value_list' => array()
          );
        }
        $productPropertyList[$key['_index']]['value_list'][$value['_index']] =
        $value['name'];
        $shoppingValueIdList[] = $value['shopping_id'];
      }
      sort($shoppingValueIdList);
      ksort($productPropertyList);
      $shoppingPropertyList = array();
      foreach ($productPropertyList as $property) {
        $item = $property['name']."\n";
        ksort($property['value_list']);
        $item .= implode("\n", $property['value_list']);
        $shoppingPropertyList[] = $item;
      }
      $shoppingPropertyTextList = implode("\n\n", $shoppingPropertyList);
      $shoppingValueIdTextList = implode(' ', $shoppingValueIdList);
      $imagePath = null;
      if ($shoppingProduct === false) {
        $imagePath = SyncImage::allocateImageFolder();
      }
      if (isset($product['price_to_x_100']) === false) {
        $product['price_to_x_100'] = null;
      }
      if ($shoppingProduct === false) {
        $keywordTextList = self::getKeywordTextList(
          $product['title'], self::$categoryName, $shoppingPropertyTextList
        );
        $columnList = array(
          'merchant_id' => 1,//TODO
          'merchant_product_id' => $product['merchant_product_id'],
          'uri_argument_list' => $product['merchant_product_id'],//TODO
          'image_path' => $imagePath,
          'image_digest' => $product['image_digest'],
          'title' => $product['title'],
          'price_from_x_100' => $product['price_from_x_100'],
          'price_to_x_100' => $product['price_to_x_100'],
          'category_name' => self::$categoryName,
          'property_list' => $shoppingPropertyTextList,
          'agency_name' => $product['agency_name'],
          'keyword_list' => $keywordTextList,
          'value_id_list' => $shoppingValueIdTextList
        );
        Db::insert('product', $columnList);
        $shoppingProductId = Db::getLastInsertId();
        CommandSyncFile::insertProduct($columnList, $shoppingProductId);
        SyncImage::bind(
          self::$categoryId,
          $shoppingProductId,
          $product['merchant_product_id'],
          $imagePath
        );
        continue;
      }
      $replacementColumnList = array();
      if ($shoppingProduct['uri_argument_list'] !== $product['merchant_product_id']) {
        $replacementColumnList['uri_argument_list'] = $product['merchant_product_id'];
      }
      if ($shoppingProduct['image_digest'] !== $product['image_digest']) {
        $replacementColumnList['image_digest'] = $product['image_digest'];
        SyncImage::bind(
        self::$categoryId,
        $shoppingProductId,
        $product['merchant_product_id'],
        $shoppingProduct['image_path']
        );
      }
      if ($shoppingProduct['title'] !== $product['title']) {
        $replacementColumnList['title'] = $product['title'];
      }
      if ($shoppingProduct['price_from_x_100'] !== $product['price_from_x_100']) {
        $replacementColumnList['price_from_x_100'] = $product['price_from_x_100'];
      }
      if ($shoppingProduct['price_to_x_100'] !== $product['price_to_x_100']) {
        $replacementColumnList['price_to_x_100'] = $product['price_to_x_100'];
      }
      if ($shoppingProduct['category_name'] !== self::$categoryName) {
        $replacementColumnList['category_name'] = self::$categoryName;
      }
      if ($shoppingProduct['property_list'] !== $shoppingPropertyTextList) {
        $replacementColumnList['property_list'] = $shoppingPropertyTextList;
      }
      if ($shoppingProduct['agency_name'] !== $product['agency_name']) {
        $replacementColumnList['agency_name'] = $product['agency_name'];
      }
      //TODO 如果分词算法/字典更新，所有 keywords 都会更新
      if (isset($replacementColumnList['title'])
          || isset($replacementColumnList['category_name'])
          || isset($replacementColumnList['property_list']) || true) {
        $keywordTextList = self::getKeywordTextList(
          $product['title'], self::$categoryName, $shoppingPropertyTextList
        );
        $keywordListByKey = array();
        foreach (explode(' ', $keywordTextList) as $keyword) {
          $keywordListByKey[$keyword] = true;
        }
        $currentKeywordList = explode(' ', $keywordTextList);
        $isUpdated = false;
        foreach ($currentKeywordList as $item) {
          if (isset($keywordListByKey[$item])) {
            unset($keywordListByKey[$item]);
            continue;
          }
          $isUpdated = true;
          break;
        }
        if ($isUpdated === false && count($keywordListByKey) !== 0) {
          $isUpdated = true;
        }
        if ($isUpdated) {
          $replacementColumnList['keyword_list'] = $keywordTextList;
        }
      }
      if ($shoppingProduct['value_id_list'] !== $shoppingValueIdTextList) {
        $replacementColumnList['value_id_list'] = $shoppingValueIdTextList;
      }
      if (count($replacementColumnList) > 0) {
        Db::update(
          'product', $replacementColumnList, 'id = ?', $shoppingProduct['id']
        );
        CommandSyncFile::updateProduct(
          $shoppingProduct['id'], $replacementColumnList
        );
      }
    }
  }

   private static function getKeywordTextList(
     $title, $categoryName, $shoppingPropertyTextList
   ) {
    $keywords = $title;
    $keywords .= ' '.$categoryName;
    $keywords .= ' '.$shoppingPropertyTextList;
    $keywords = SegmentationService::execute($keywords);
    $list = explode(' ', $keywords);
    return implode(' ', array_unique($list));
  }
}