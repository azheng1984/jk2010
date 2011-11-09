<?php
class ProductContentUpdater {
  public function execute($item) {
    $product = DbProduct::get($item['product_id']);
    $this->updateCategory($product['category_id']);
    $product['properties'] = $this->updateProperties(
      $product['merchant_product_id'], $product['category_id']
    );
    $this->updateWebDb($product);
    $this->updateSearchDb($product);
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
        $index = DbCategoryKeyCount::getCount($categoryId);
        if ($index >= 10) { //TODO
          continue;
        }
        DbCategoryKeyCount::moveNext($categoryId, ++$index);
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
    $webProductId = $product['web_product_id'];
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
    return DbProduct::updateWebContent(
      $webProductId, $categoryId, $title, $description
    );
  }

  private function updateSearchDb($product) {
    $webProductId = $product['web_product_id'];
    $categoryId = $product['category_id'];
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
    $content = $title.' '.Segmentation::execute($product['description']).' '.$properties;
    $product['categories'] = null;
    Segmentation::execute($product['categories']);
    DbProduct::updateSearchContent(
      $webProductId, $categoryId, $keyIdList2, $content
    );
    DbProduct::resetSearchValueIdList($webProductId);
    foreach ($valueIdList as $index => $item) {
      $valueIdList2 = implode(',', $item);
      DbProduct::updateSearchValueIdList($webProductId, $index, $valueIdList2);
    }
  }
}