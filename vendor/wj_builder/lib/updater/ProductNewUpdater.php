<?php
class ProductNewUpdater {
  public function execute($item) {
    $product = DbProduct::get($item['id']);
    $this->updateCategory($product['category_id']);
    $product['properties'] = $this->updateProperties(
      $product['merchant_product_id'], $product['category_id']
    );
    $this->updateWebDb($product);
    //$this->updateSearchDb($product);
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
    foreach (DbProperty::getList($merchantProductId) as $product) {
      $value = DbProperty::getValue($product['property_value_id']);
      $key = DbProperty::getKey($value['key_id']);
        $webKey = DbProperty::getWebKey($categoryId, $key['key']);
      if ($webKey === false) {
        $webKey = DbProperty::insertIntoWebKey($categoryId, $key['key']);
      }
      $webValue = DbProperty::getWebValue($categoryId, $value['value']);
      if ($webValue === false) {
        $webValue = DbProperty::insertIntoWebKey($categoryId, $value['value']);
      }
      $properties[] = array('key' => $webKey, 'value' => $webValue);
    }
  }

  private function updateWebDb($product) {
    $lowestPriceX100 = $product['lowest_product'] * 100;
    $highestPriceX100 = null;
    if ($product['highest_product'] !== null) {
      $highestPriceX100 = $product['highest_product'] * 100;
    }
    $cutPriceX100 = 0;
    $merchantId = 1;
    $url = 'product/'.$product['merchant_product_id'].'.html';
    $imageDbIndex = 0;
    $categoryId = $product['category_id'];
    $title = $product['title'];
    $propertyList = '';
    foreach ($product['properties'] as $item) {
      $key = $item['key'];
      $value = $item['value'];
      $propertyList[] = $key['key'].':'.$value['value'];
    }
    $properties = implode(', ', $propertyList);
    $description = $product['description'];
    DbProduct::insertIntoWeb(
      $lowestPriceX100, $highestPriceX100, $cutPriceX100, $merchantId, $url,
      $imageDbIndex, $categoryId, $title, $properties, $description
    );
  }

  private function updateSearchDb($product) {
    DbProduct::insertIntoSearch(
      $id, $lowestPriceX100, $cutPriceX100, $saleRank, $categoryId,
      $propertyIdList, $title, $properties, $description
    );
    Segmentation::execute($product['title']);
    Segmentation::execute($product['description']);
    Segmentation::execute($product['properties']);
    Segmentation::execute($product['categories']);
  }
}