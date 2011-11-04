<?php
class ProductNewUpdater {
  public function execute($item) {
    $product = DbProduct::get($item['id']);
    $this->updateCategory($product['category_id']);
    $product['properties'] = $this->updateProperties(
      $product['merchant_product_id'], $product['category_id']
    );
    $webProductId = $this->updateWebDb($product);
    $this->updateSearchDb($product, $webProductId);
    exit;
  }

  private function updateCategory($id) {
    $target = DbCategory::getWeb($id);
    if ($target === false) {
      $product = DbCategory::get($id);
      DbCategory::insertIntoWeb($id, $product['name']);
    }
  }

  private function updateProperties($merchantProductId, $categoryId) {
    $properties = array();
    foreach (DbProperty::getList($merchantProductId) as $item) {
      $value = DbProperty::getValue($item['property_value_id']);
      $key = DbProperty::getKey($value['key_id']);
      $webKey = DbProperty::getWebKey($categoryId, $key['key']);
      if ($webKey === false) {
        $webKey = DbProperty::insertIntoWebKey($categoryId, $key['key']);
      }
      $webValue = DbProperty::getWebValue($categoryId, $value['value']);
      if ($webValue === false) {
        $webValue = DbProperty::insertIntoWebValue($categoryId, $value['value']);
      }
      $properties[] = array('key' => $webKey, 'value' => $webValue);
    }
    return $properties;
  }

  private function updateWebDb($product) {
    $lowestPriceX100 = $product['lowest_price'] * 100;
    $highestPriceX100 = null;
    if ($product['highest_price'] !== null) {
      $highestPriceX100 = $product['highest_price'] * 100;
    }
    $cutPriceX100 = 0;
    $merchantId = 1;
    $url = 'product/'.$product['merchant_product_id'].'.html';
    $imageDbIndex = 0;
    $categoryId = $product['category_id'];
    $title = $product['title'];
    $propertyList = array();
    foreach ($product['properties'] as $item) {
      $key = $item['key'];
      $value = $item['value'];
      $propertyList[] = $key['key'].':'.$value['value'];
    }
    $properties = implode(', ', $propertyList);
    $description = $product['description'];
    return DbProduct::insertIntoWeb(
      $lowestPriceX100, $highestPriceX100, $cutPriceX100, $merchantId, $url,
      $imageDbIndex, $categoryId, $title, $properties, $description
    );
  }

  private function updateSearchDb($product, $webProductId) {
    $lowestPriceX100 = $product['lowest_price'] * 100;
    $cutPriceX100 = 0;
    $categoryId = $product['category_id'];
    $saleRank = 1000000 - $product['sale_index'];
    $title = Segmentation::execute($product['title']);
    $propertyList = array();
    $propertyIdList = array();
    foreach ($product['properties'] as $item) {
      $key = $item['key'];
      $value = $item['value'];
      $propertyList[] = $key['key'].':'.$value['value'];
      $propertyIdList[] = $value['id'];
    }
    $properties = Segmentation::execute(implode(', ', $propertyList));
    $propertyIdList = implode(',', $propertyIdList);
    $description = Segmentation::execute($product['description']);
    $product['categories'] = null;
    Segmentation::execute($product['categories']);
    DbProduct::insertIntoSearch(
      $webProductId, $lowestPriceX100, $cutPriceX100, $saleRank, $categoryId,
      $propertyIdList, $title, $properties, $description
    );
  }
}