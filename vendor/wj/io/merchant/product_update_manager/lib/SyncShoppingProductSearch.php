<?php
class SyncShoppingProductSearch {
  public static function insert($product, $categoryId, $shoppingPropertyTextList, $shoppingValueIdTextList) {
    $keywords = self::getWordList($product, $shoppingPropertyTextList);
    $newProduct = array(
      'category_id' => $categoryId,
      'price_from_x_100' => $product['price_from_x_100'],
      'keyword_list' => $keywords,
      'value_id_list' => $shoppingValueIdTextList
    );
    DbConnection::connect('shopping_product_search');
    Db::insert('product', $newProduct);
    DbConnection::close();
    ShoppingCommandFile::insertProductSearch($newProduct);
  }

  private function getWordList($product, $shoppingPropertyTextList) {
    $keywords = $product['title'];
    $keywords .= ' '.$product['category_name'];
    $keywords .= ' '.$shoppingPropertyTextList;
    $keywords = SegmentationService::execute($keywords);
    $list = explode(' ', $keywords);
    return implode(' ', array_unique($list));
  }

  public static function update($product, $replacementColumnList, $shoppingCategoryId, $shoppingPropertyTextList, $shoppingValueIdTextList) {
    DbConnection::connect('shopping_product_search');
    $shoppingProductSearchProduct = Db::getRow(
      'SELECT * FROM product WHERE id = ?',
      $product['shopping_product_id']
    );
    $updateColumnList = array();
    if ($shoppingProductSearchProduct['value_id_list'] !== $shoppingValueIdTextList) {
      $updateColumnList['value_id_list'] = $shoppingValueIdTextList;
    }
    if (isset($replacementColumnList['title'])
        || isset($replacementColumnList['category_name'])
        || isset($replacementColumnList['property_list'])) {
      $keywordList = explode(' ', $shoppingProductSearchProduct['keyword_list']);
      $keywordListByKey = array();
      foreach ($keywordList as $keyword) {
        $keywordListByKey[$keyword] = true;
      }
      $keywords = self::getWordList($product, $shoppingPropertyTextList);
      $currentKeywordList = explode(' ', $keywords);
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
        $updateColumnList['keyword_list'] = $keywords;
      }
    }
    if ($shoppingProductSearchProduct['category_id'] !== $shoppingCategoryId) {
      $updateColumnList['category_id'] = $shoppingCategoryId;
    }
      if ($shoppingProductSearchProduct['price_from_x_100'] !== $product['price_from_x_100']) {
      $updateColumnList['price_from_x_100'] = $product['price_from_x_100'];
    }
    Db::update('product', $updateColumnList);
    DbConnection::close();
    ShoppingCommandFile::updateProductSearch($updateColumnList);
  }
}