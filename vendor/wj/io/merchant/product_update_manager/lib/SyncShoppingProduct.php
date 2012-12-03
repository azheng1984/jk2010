<?php
class SyncShoppingProduct {
  public static function execute(
    $categoryName, $shoppingCategoryId, $propertyList, $version, $merchantName
  ) {
    $merchantId = 1;//TODO
    DbConnection::connect($merchantName);
    $categoryId = Db::getColumn(
      'SELECT id FROM category WHERE name = ?', $categoryName
    );
    $productList = Db::getAll(
      'SELECT * FROM product WHERE category_id = ?', $categoryId
    );
    DbConnection::close();
    foreach ($productList as $product) {
      if ($product['version'] < $version) {
        $shoppingProduct = Db::get(
          'SELECT id, image_path FROM product'
            .' WHERE merchant_id = ? AND merchant_product_id = ?',
          1,
          $product['merchant_product_id']
        );
        ShoppingCommandFile::deleteProduct($shoppingProduct['id']);
        SyncShoppingImage::delete($shoppingProduct['image_path']);
        Db::delete('product', 'id = ?', 1, $shoppingProduct['id']);
        continue;
      }
      DbConnection::connect($merchantName);
      $valueList = Db::getAll(
        'SELECT * FROM product_property_value WHERE merchant_product_id = ?',
        $product['merchant_product_id']
      );
      DbConnection::close();
      $productPropertyList = array();
      $shoppingValueIdList = array();
      foreach ($valueList as $value) {
        $value = $propertyList['value_list'][$value['property_value_id']];
        $key = $propertyList['key_list'][$value['key_id']];
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
      $shoppingPropertyTextList = implode("\n", $shoppingPropertyList);
      $shoppingValueIdTextList = implode(' ', $shoppingValueIdList);
      $shoppingProduct = Db::getRow(
        'SELECT * FROM product'
          .' WHERE merchant_id = ? AND merchant_product_id = ?',
        $merchantId, $product['merchant_product_id']
      );
      $imagePath = null;
      if ($shoppingProduct === false
        || $shoppingProduct['image_digest'] !== $product['image_digest']) {
        $imagePath = SyncShoppingImage::getImagePath();
      }
      if (isset($product['price_to_x_100']) === false) {
        $product['price_to_x_100'] = null;
      }
      if ($shoppingProduct === false) {
        $keywordTextList = self::getList(
          $product, $categoryName, $shoppingPropertyTextList
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
          'category_name' => $categoryName,
          'property_list' => $shoppingPropertyTextList,
          'agency_name' => $product['agency_name'],
          'keyword_list' => $keywordTextList,
          'value_id_list' => $shoppingValueIdTextList
        );
        Db::insert('product', $columnList);
        $shoppingProductId = Db::getLastInsertId();
        ShoppingCommandFile::insertProduct($columnList, $shoppingProductId);
        SyncShoppingImage::execute(
          $categoryName, $shoppingProductId, $product['merchant_product_id'], $imagePath
        );
        continue;
      }
      $replacementColumnList = array();
      if ($shoppingProduct['uri_argument_list'] !== $product['merchant_product_id']) {
        $replacementColumnList['uri_argument_list'] = $product['merchant_product_id'];
      }
      var_dump($shoppingProduct);
      if ($shoppingProduct['image_path'] !== $product['image_path']) {
        $replacementColumnList['image_path'] = $product['image_path'];
      }
      if ($shoppingProduct['image_digest'] !== $product['image_digest']) {
        $replacementColumnList['image_digest'] = $product['image_digest'];
        SyncShoppingImage::execute(
          $categoryId, $shoppingProductId, $product['merchant_product_id'], $imagePath
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
      if ($shoppingProduct['category_name'] !== $categoryName) {
        $replacementColumnList['category_name'] = $categoryName;
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
          || isset($replacementColumnList['property_list'])) {
        $keywordList = explode(' ', $product['keyword_list']);
        $keywordListByKey = array();
        foreach ($keywordList as $keyword) {
          $keywordListByKey[$keyword] = true;
        }
        $keywordTextList = self::getList(
          $product, $categoryName, $shoppingPropertyTextList
        );
        $currentKeywordList = explode(' ', $keywordTextList);
        $isUpdated = false;
        foreach ($currentKeywordList as $item) {
          if (isset($keywordListByKey[$item])) {
            unset($keywordListByKey[$itme]);
            continue;
          }
          $isUpdated = true;
          break;
        }
        if ($isUpdated !== true && count($keywordListByKey) !== 0) {
          $isUpdated = true;
        }
        if ($isUpdated) {
          $replacementColumnList['keyword_list'] = $keywordTextList;
        }
      }
      if ($shoppingProduct['value_id_list'] !== $shoppingValueIdTextList) {
        $replacementColumnList['value_id_list'] = $shoppingValueIdTextList;
      }
      Db::update('product', $replacementColumnList);
      ShoppingCommandFile::updateProduct($replacementColumnList);
    }
  }

   private static function getList(
    $product, $categoryName, $shoppingPropertyTextList
   ) {
    $keywords = $product['title'];
    $keywords .= ' '.$categoryName;
    $keywords .= ' '.$shoppingPropertyTextList;
    $keywords = SegmentationService::execute($keywords);
    $list = explode(' ', $keywords);
    return implode(' ', array_unique($list));
  }
}