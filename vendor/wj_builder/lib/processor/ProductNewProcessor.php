<?php
class ProductNewProcessor {
  private $spiderProduct;
  private $webCategoryId;
  private $webValueIdLists = array();
  private $webKeyIdList = array();

  public function execute($item) {
    $this->spiderProduct = DbSpiderProduct::get($item['id']);
    $this->syncWebCategory();
    $this->syncPropertyList();
    $this->syncProduct();
  }

  private function syncCategory() {
    $spiderCategory = DbSpiderCategory::get(
      $this->spiderProduct['category_id']
    );
    $webCategory = DbWebCategory::get($spiderCategory['name']);
    if ($webCategory === false) {
      $this->webCategoryId = DbWebCategory::insert($spiderCategory['name']);
      DbSearchCategory::insert($this->webCategoryId);
      return;
    }
    $this->webCategoryId = $webCategory['id'];
  }

  private function syncPropertyList() {
    $spiderProductPropertyList = DbSpiderProduct::getPropertyValueList(
      $this->spiderProduct['id']
    );
    $spiderPropertyList = array();
    foreach ($spiderProductPropertyList as $spiderProductProperty) {
      $spiderValue = DbSpiderValue::get(
        $spiderProductProperty['property_value_id']
      );
      if (!isset($spiderPropertyList[$spiderValue['key_id']])) {
        $spiderKey = DbSpiderKey::get($spiderValue['key_id']);
        $spiderKey['value_list'] = array();
        $spiderPropertyList[$spiderKey['id']] = $spiderKey;
      }
      $spiderPropertyList[$spiderKey['id']]['value_list'][] = $spiderValue;
    }
    foreach ($spiderPropertyList as $spiderKey) {
      $webKey = $this->syncWebKey($spiderKey['name']);
      $webValueIdList = $this->syncWebValueList(
        $spiderKey['value_list'], $webKey['id']
      );
      $this->webValueIdLists[$spiderKey['mva_index']] = $webValueIdList;
      $webKeyIdList[] = $webKey['id'];
    }
  }

  private function syncKey($spiderKeyName) {
    $webKey = DbWebKey::get($this->webCategoryId, $spiderKeyName);
    if ($webKey === false) {
      $mvaIndex = DbBuilderKeyMvaIndex::getNext($this->webCategoryId);
      $id = DbWebKey::insert($this->webCategoryId, $spiderKeyName, $mvaIndex);
      DbSearchKey::insert($id, $this->webCategoryId);
      $webKey = array(
        'id' => $id, 'name' => $spiderKeyName,
        'category_id' => $this->webCategoryId, 'mva_index' => $mvaIndex
      );
    }
    return $webKey;
  }

  private function syncValueList($spiderValueList, $webKeyId) {
    $result = array();
    foreach ($spiderValueList as $spiderValue) {
      $webValue = DbWebValue::get($webKeyId, $spiderValue['name']);
      if ($webValue === false) {
        $id = DbWebValue::insert($webKeyId, $spiderValue['name']);
        DbSearchValue::insert($id, $this->webCategoryId);
        $webValue = array('id' => $id);
      }
      $result[] = $webValue['id'];
    }
    return $result;
  }

  private function syncProduct() {
    $webProduct = DbBuilderSpiderProduct::get(
      $this->spiderProduct['merchant_product_id']
    );
    if ($webProduct === false) {
      $spiderProduct = $this->spiderProduct;
      $lowestPriceX100 = $spiderProduct['lowest_price_x_100'];
      $highestPriceX100 = $spiderProduct['highest_price_x_100'];
      $listLowestPriceX100 = null;
      $merchantId = 1;
      $uri = $spiderProduct['merchant_product_id'].'.html';
      $imageDbIndex = 0;
      $categoryId = $this->webCategoryId;
      $title = $spiderProduct['title'];
      $description = $spiderProduct['description'];
      $webProductId = DbWebProduct::insert(
        $lowestPriceX100, $highestPriceX100, $listLowestPriceX100, $merchantId,
        $imageDbIndex, $categoryId, $uri, $title, $description
      );
      DbBuilderSpiderProduct::insert(
        $merchantId,
        $this->spiderProduct['merchant_product_id'],
        $webProductId
      );
      $discountX10 = 100;
      $saleRank = 1000000 - $spiderProduct['sale_index'];
      $publishTimestamp = date("ymdHi");
      DbSearchProduct::insert(
        $webProductId, $lowestPriceX100, $discountX10,
        $saleRank, $publishTimestamp, $categoryId,
        implode(',', $this->webKeyIdList),
        Segmentation::execute($spiderProduct['title'])
          .' '.Segmentation::execute($spiderProduct['description'])
      );
      foreach ($this->webValuesList as $index => $values) {
        DbSearchProduct::updateValues(
          $webProductId, $index, implode(',', $values)
        );
      }
      return;
    }
    $this->updateProduct();
  }

  private function updateProduct() {
    /* do same thing with content processor */
  }
}