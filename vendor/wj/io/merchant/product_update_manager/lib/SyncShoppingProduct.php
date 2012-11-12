<?php
class SyncShoppingProduct {
  private function execute(
    $categoryName, $categoryId, $propertyList, $version, $merchantName
  ) {
    $productList = Db::getAll(
      'SELECT * FROM product WHERE category_id = ?', $categoryId
    );
    foreach ($productList as $product) {
      if ($product['version'] < $version) {
        ShoppingCommandFile::deleteProduct($product['shopping_id']);
        ShoppingCommandFile::deleteProductSearch($product['shopping_id']);
        SyncShoppingImage::delete($product['shopping_id']);
      }
      $valueList = Db::getAll(
        'SELECT * FROM product_property_value WHERE merchant_product_id = ?',
        $product['merchant_product_id']
      );
      $productPropertyList = array();
      $shoppingValueIdList = array();
      foreach ($valueList as $value) {
        $value = $propertyList['value_list'][$value['property_value_id']];
        $key = $$propertyList['key_list'][$value['key_id']];
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
      $shoppingProduct = null;
      if ($product['shopping_product_id'] !== null) {
        $shoppingProduct = Db::getRow(
          'SELECT * FROM product WHERE id = ?', $product['shopping_product_id']
        );
      }
      $imagePath = null;
      if ($shoppingProduct === null
        || $shoppingProduct['image_digest'] !== $product['image_digest']) {
        $imagePath = SyncShoppingImage::getImagePath();
      }
      if (isset($product['price_to_x_100'])) {
        $product['price_to_x_100'] = null;
      }
      if ($shoppingProduct === null) {
        $columnList = array(
          'merchant_id' => 1,//TODO
          'uri_argument_list' => $product['merchant_product_id'],//TODO
          'image_path' => $imagePath,
          'image_digest' => $product['image_digest'],
          'title' => $product['title'],
          'price_from_x_100' => $product['price_from_x_100'],
          'price_to_x_100' => $product['price_to_x_100'],
          'category_name' => $categoryName,
          'property_list' => $shoppingPropertyTextList,
          'agency_name' => $product['agency_name']
        );
        DbConnection::connect('shopping');
        Db::insert('product', $columnList);
        $shoppingProductId = Db::getLastInsertId();
        DbConnection::close();
        Db::update(
          'product',
          array('shopping_product_id' => $shoppingProductId),
          'id = ?', $product['id']
        );
        SyncShoppingImage::execute($categoryId, $shoppingProductId, $imagePath);
        ShoppingCommandFile::insertProduct($columnList);
        SyncShoppingProductSearch::insert($product, $shoppingValueIdTextList);
      }
      $replacementColumnList = array();
      if ($shoppingProduct['uri_argument_list'] !== $product['merchant_product_id']) {
        $replacementColumnList['uri_argument_list'] = $product['merchant_product_id'];
      }
      if ($shoppingProduct['image_path'] !== $product['image_path']) {
        $replacementColumnList['image_path'] = $product['image_path'];
      }
      if ($shoppingProduct['image_digest'] !== $product['image_digest']) {
        $replacementColumnList['image_digest'] = $product['image_digest'];
        SyncShoppingImage::execute($categoryId, $shoppingProductId, $imagePath);
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
      DbConnection::connect('shopping');
      Db::update('product', $replacementColumnList);
      DbConnection::close();
      ShoppingCommandFile::updateProduct($replacementColumnList);
      SyncShoppingProductSearch::update($product, $shoppingValueIdTextList);
    }
  }
}