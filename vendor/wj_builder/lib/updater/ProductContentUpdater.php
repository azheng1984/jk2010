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
      $webValue = DbProperty::getWebValue($key['id'], $value['value']);
      if ($webValue === false) {
        $webValue = DbProperty::insertIntoWebValue($key['id'], $value['value']);
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
    $description = $product['description'];
    return DbProduct::updateWebContent(
      $webProductId, $categoryId, $title, $properties, $description
    );
  }

  private function updateSearchDb($product) {
    $webProductId = $product['web_product_id'];
    $categoryId = $product['category_id'];
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
    DbProduct::updateSearchContent(
      $webProductId, $categoryId, $propertyIdList,
      $title, $properties, $description
    );
  }
}