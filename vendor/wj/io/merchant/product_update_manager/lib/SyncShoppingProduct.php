<?php
class SyncShoppingProduct {
  private function execute($categoryId) {
    $productList = Db::getAll(
      'SELECT * FROM product WHERE category_id = ?', $this->categoryId
    );
    foreach ($productList as $product) {
      if ($product['version'] < $GLOBALS['VERSION']) {
        //delete product
      }
      $valueList = Db::getAll(
          'SELECT * FROM product_property_value WHERE merchant_product_id = ?',
          $product['merchant_product_id']
      );
      $propertyList = array();
      $shoppingValueIdList = array();
      foreach ($valueList as $value) {
        $value = $this->valueList[$value['property_value_id']];
        $key = $this->keyList[$value['key_id']];
        if (!isset($propertyList[$key['_index']])) {
          $propertyList[$key['_index']] = array(
              'name' => $key['name'], 'value_list' => array()
          );
        }
        $propertyList[$key['_index']]['value_list'][$value['_index']]
        = $value['name'];
        $shoppingValueIdList[] = $value['shopping_id'];
      }
      sort($shoppingValueIdList);
      ksort($propertyList);
      $shoppingPropertyList = array();
      foreach ($propertyList as $property) {
        $item = $property['name']."\n";
        ksort($property['value_list']);
        $item .= implode("\n", $property['value_list']);
        $shoppingPropertyList []= $item;
      }
      $shoppingPropertyTextList = implode("\n", $shoppingPropertyList);
      $merchantPath = $this->getMerchantPath($product['merchant_id']);
      $image = ImageDb::get($this->categoryId, $product['id']);
      $imagePath = $this->getImagePath();
      if ($product['shopping_product_id'] === null) {
        Db::insert('product', array(
        'merchant_path' => $merchantPath,
        'merchant_uri_argument_list' => $product['merchant_product_id'],
        'price_from_x_100' => $product['price_from_x_100'],
        'image_path' => $imagePath,
        'image_digest' => md5($image),
        ));
        $shoppingProductId = Db::getLastInsertId();
        $imageStagingFolder = '/home/azheng/image_staging/jingdong/';
        file_put_contents(
        $imageStagingFolder.$imagePath.$shoppingProductId.'.jpg', $image
        );
        $this->syncImage($image, $product['id'], $shoppingProductId);
        $this->output .= 'INSERT INTO product';//TODO
        $shoppingValueIdTextList = implode(' ', $shoppingValueIdList);
        $keywords = $product['title'];
        $keywords .= ' '.$this->categoryName;
        $keywords .= ' '.$shoppingPropertyTextList;
        $keywords .= ' '.$merchantPath;
        $this->syncProuctSearch(
            $keywords, $shoppingValueIdTextList, $product['price_from_x_100']
        );
        continue;
      }
      //TODO:update 本地 shopping portal
      DbConnection::connect('shopping_portal');
      $shoppingProduct = Db::getRow(
          'SELECT * FROM product WHERE id = ?',
          $product['shopping_product_id']
      );
      if ($shoppingProduct['property_list'] !== $shoppingPropertyTextList) {
  
      }
      if ($shoppingProduct['category_name'] !== $this->categoryName) {
      }
      if ($shoppingProduct['price_from_x_100'] !== $product['price_from_x_100']) {
      }
      if ($product['is_image_updated']) {
      }
      DbConnection::connect('shopping_product_search');
      $shoppingProductSearchProduct = Db::getRow(
          'SELECT * FROM product WHERE id = ?',
          $product['shopping_product_id']
      );
      if ($shoppingProductSearchProduct['value_id_list'] !== $shoppingValueIdTextList) {
        //TODO:update value id list
      }
      $keywordList = explode(' ', $shoppingProductSearchProduct['keyword_list']);
      $keywordListByKey = array();
      foreach ($keywordList as $keyword) {
        $keywordListByKey[$keyword] = true;
      }
      $currentKeywordList = array_unique(explode(' ', $keywords));
      $isUpdated = false;
      foreach ($currentKeywordList as $item) {
        if (isset($keywordListByKey[$item])) {
          unset($keywordListByKey[$itme]);
          continue;
        }
        $isUpdated = true;
        break;
      }
      if (count($keywordListByKey) !== 0) {
        $isUpdated = true;
      }
      if ($isUpdated) {
        //TODO:update keyword list
      }
      //TODO:update product search db & record sql
    }
  }

  private function getMerchantPath($merchantId) {
    $name = Db::getColumn('SELECT name FROM merchant WHERE id = ?', $merchantId);
    return "京东商城\n".$name;
  }
}