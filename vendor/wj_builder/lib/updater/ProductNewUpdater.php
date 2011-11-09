<?php
class ProductNewUpdater {
  public function execute($item) {
    $product = DbProduct::get($item['product_id']);
    $this->updateCategory($product['category_id']);
    $product['properties'] = $this->updateProperties(
      $product['merchant_product_id'], $product['category_id']
    );
    $webProductId = $this->updateWebDb($product);
    DbProduct::updateWebProductId($item['product_id'], $webProductId);
    $this->updateSearchDb($product, $webProductId);
  }

  private function updateCategory($id) {
    $target = DbCategory::getWeb($id);//TODO:by name
    if ($target === false) {
      $product = DbCategory::get($id);
      DbCategory::insertIntoWeb($id, $product['name']);
      DbCategoryKeyCount::insert($id);
    }
  }

  private function updateProperties($merchantProductId, $categoryId) {
    $properties = array();
    foreach (DbProperty::getList($merchantProductId) as $item) {
      $value = DbProperty::getValue($item['property_value_id']);
      $key = DbProperty::getKey($value['key_id']);
      $webKey = DbProperty::getWebKey($categoryId, $key['key']);
      if ($webKey === false) {
        $index = DbCategoryKeyCount::getCount($categoryId);
        if ($index >= 10) { //TODO
          continue;
        }
        ++$index;
        DbCategoryKeyCount::moveNext($categoryId, $index);
        $webKey = DbProperty::insertIntoWebKey($categoryId, $key['key'], $index);
      }
      $webValue = DbProperty::getWebValue($webKey['id'], $value['value']);
      if ($webValue === false) {
        $webValue = DbProperty::insertIntoWebValue($webKey['id'], $value['value']);
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
    $url = $product['merchant_product_id'].'.html';
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
    $description = $product['description'].' '.$properties;
    return DbProduct::insertIntoWeb(
      $lowestPriceX100, $highestPriceX100, $cutPriceX100, $merchantId, $url,
      $imageDbIndex, $categoryId, $title, $description
    );
  }

  private function updateSearchDb($product, $webProductId) {
    $lowestPriceX100 = $product['lowest_price'] * 100;
    $cutPriceX100 = 0;
    $categoryId = $product['category_id'];
    $saleRank = 1000000 - $product['sale_index'];
    $title = Segmentation::execute($product['title']);
    $propertyList = array();
    $keyIdList = array();
    $valueIdList = array();
    foreach ($product['properties'] as $item) {
      $key = $item['key'];
      $value = $item['value'];
      $propertyList[] = $key['key'].':'.$value['value'];
      if (isset($valueIdList[$key['mva_index']])) {
        $valueIdList[$key['mva_index']] = array();
      }
      $valueIdList[$key['mva_index']][] = $value['id'];
      $keyIdList[$key['id']] = true;
    }
    $keyIdList = array_keys($keyIdList);
    $properties = Segmentation::execute(implode(', ', $propertyList));
    $keyIdList2 = implode(',', $keyIdList);
    $content = $title.' '.Segmentation::execute($product['description'])
      .' '.$properties;
    $product['categories'] = null;
    Segmentation::execute($product['categories']);
    $id = DbProduct::insertIntoSearch(
      $webProductId, $lowestPriceX100, $cutPriceX100, $saleRank, $categoryId,
      $keyIdList2, $content
    );
    foreach ($valueIdList as $index => $item) {
      $valueIdList2 = implode(',', $item);
      DbProduct::updateSearchValueIdList($webProductId, $index, $valueIdList2);
    }
  }
}