<?php
class ProductContentProcessor {
  private $spiderProduct;
  private $webCategoryId;
  private $searchValueIdLists = array();
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
      $searchValueIdList = $this->syncValueList(
        $spiderKey['value_list'], $webKey['id']
      );
      $this->webValueIdLists[intval($spiderKey['mva_index'])]
         = $searchValueIdList;
      $this->webKeyIdList[] = $webKey['id'];
    }
  }

  private function syncKey($spiderKeyName) {
    $webKey = DbWebKey::get($this->webCategoryId, $spiderKeyName);
    if ($webKey === false) {
      $mvaIndex = DbBuilderKeyMvaIndex::getNext($this->webCategoryId);
      $id = DbWebKey::insert($this->webCategoryId, $spiderKeyName, $mvaIndex);
      DbSearchKey::insert($id, $this->webCategoryId);
      $webKey = array(
        'id' => $id,
        'name' => $spiderKeyName,
        'category_id' => $this->webCategoryId,
        'mva_index' => $mvaIndex
      );
    }
    return $webKey;
  }

  private function syncValueList($spiderValueList, $webKeyId) {
    $searchValueIdList = array();
    foreach ($spiderValueList as $spiderValue) {
      $webValue = DbWebValue::get($webKeyId, $spiderValue['name']);
      if ($webValue === false) {
        $id = DbWebValue::insert($webKeyId, $spiderValue['name']);
        DbSearchValue::insert($id, $this->webCategoryId);
        $webValue = array('id' => $id);
      }
      $searchValueIdList[] = $webValue['id'];
    }
    return $searchValueIdList;
  }

  private function syncProduct() {
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
    $keywordList = Segmentation::execute($spiderProduct['title'])
          .' '.Segmentation::execute($spiderProduct['description']);
    $keyIdList = implode(',', $this->webKeyIdList);
    $discountX10 = 100;
    $saleRank = 1000000 - $spiderProduct['sale_index'];
    $publishTimestamp = date("ymdHi");
    $valueIdLists = $this->getSearchValueIdLists();
    $spiderProductWebProduct = DbBuilderSpiderProductWebProduct::get(
      $this->spiderProduct['merchant_product_id']
    );
    if ($spiderProductWebProduct === false) {
      $this->webProductId = DbWebProduct::insert(
        $lowestPriceX100,
        $highestPriceX100,
        $listLowestPriceX100,
        $merchantId,
        $imageDbIndex,
        $categoryId,
        $uri,
        $title,
        $description
      );
      DbSearchProduct::insert(
        $this->webProductId,
        $lowestPriceX100,
        $discountX10,
        $saleRank,
        $publishTimestamp,
        $categoryId,
        $keyIdList,
        $keywordList,
        $valueIdLists
      );
      DbBuilderSpiderProductWebProduct::insert(
        $merchantId,
        $this->spiderProduct['merchant_product_id'],
        $this->webProductId
      );
      return;
    }
    $this->webProductId = $spiderProductWebProduct['web_product_id'];
    DbWebProduct::update(
      $this->webProductId,
      $lowestPriceX100,
      $highestPriceX100,
      $listLowestPriceX100,
      $imageDbIndex,
      $merchantId,
      $categoryId,
      $uri,
      $title,
      $description
    );
    DbSearchProduct::updateContent(
      $this->webProductId,
      $lowestPriceX100,
      $discountX10,
      $saleRank,
      $publishTimestamp,
      $categoryId,
      $keyIdList,
      $keywordList,
      $valueIdLists
    );
  }

  private function getSearchValueIdLists() {
    $result = array();
    for ($index = 1; $index <= 10; ++$index) {
      if (isset($this->result[$index])) {
        $this->result[$index] = implode(',', $this->webValuesList[$index]);
        continue;
      }
      $this->result[$index] = null;
    }
    return $result;
  }
}