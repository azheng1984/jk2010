<?php
class ProductNewProcessor {
  private $spiderProduct;
  private $webCategoryId;
  private $webPropertyList = array();

  public function execute($item) {
    $spiderProduct = DbSpiderProduct::get($item['id']);
    $webCategoryId = $this->updateWebCategory($spiderProduct['category_id']);
  }

  private function updateWebCategory($spiderCategoryId) {
    $spiderCategory = DbSpiderCategory::get($spiderCategoryId);
    $webCategory = DbWebCategory::get($spiderCategory['name']);
    if ($webCategory === false) {
      $webCategory = DbWebCategory::insert($spiderCategory['name']);
    }
    return $webCategory['id'];
  }

  private function updateWebKey($webCategoryId, $spiderProductId) {
    foreach (DbSpiderKey::getList($spiderProductId) as $spiderKey) {
      $webKey = DbWebKey::get($webCategoryId, $spiderKey['name']);
      if ($webKey === false) {
        //get new uri_index & mva_index
        DbWebKey::insert();
      }
      $webValues = $this->updateWebValues($webKey['id'], $spiderKey['id'], $spiderProductId);
      $this->webPropertyList[] = array('key' => $webKey, 'value_list' => $webValues);
    }
  }

  private function updateWebValues($webKeyId, $spiderKeyId, $spiderProductId) {
    $webValues = array();
    $spiderValues = DbSpiderValue::getList($spiderProductId, $spiderKeyId);
    foreach ($spiderValues as $spiderValue) {
      $webValue = DbWebValue::get($webKeyId, $spiderValue['name']);
      if ($webValue === false) {
        //get new uri_index
        DbWebValue::insert();
      }
      $webValues[] = $webValue;
    }
    return $webValues;
  }

  private function updateWebProduct() {
    
  }

  private function updateSearchCategory() {
    
  }

  private function updateSearchKey() {
    
  }

  private function updateSearchValues() {
    
  }

  private function updateSearchProduct() {
    
  }

  private function updateSpiderProduct() {
    
  }
}