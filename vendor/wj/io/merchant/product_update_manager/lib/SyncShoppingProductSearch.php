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
    //TODO
    $list = explode(' ', $text);
    return array_unique($list);
  }

  public static function update($product, $shoppingCategoryId, $shoppingPropertyTextList, $shoppingValueIdTextList) {
    DbConnection::connect('shopping_product_search');
    $shoppingProductSearchProduct = Db::getRow(
      'SELECT * FROM product WHERE id = ?',
      $product['shopping_product_id']
    );
    //TODO:如果 title/category_name/property value_id_list 都没有变化，直接跳过
    $updateColumnList = array();
    if ($shoppingProductSearchProduct['value_id_list'] !== $shoppingValueIdTextList) {
      $updateColumnList['value_id_list'] = $shoppingValueIdTextList;
    }
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
    if ($isCategoryUpdated) {
      $updateColumnList['category_id'] = $categoryId;
    }
    if ($isPriceUpdated) {
      $updateColumnList['price_from_x_100'] = $priceFromX100;
    }
    Db::update('product', $updateColumnList);
    DbConnection::close();
    ShoppingCommandFile::updateProductSearch($updateColumnList);
  }
}