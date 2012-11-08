<?php
class SyncShoppingProductSearch {
  private function getWordList($text) {
    //TODO
    $list = explode(' ', $text);
    return array_unique($list);
  }
  
  private function syncProuctSearch($keywords, $shoppingValueIdTextList, $price) {
    //TODO:update
    DbConnection::connect('product_search');
    $keywordList = $this->getWordList($keywords);
    Db::insert('product', array(
    'category_id' => $this->shoppingCategoryId,
    'price_from_x_100' => $price,
    'value_id_list' => $shoppingValueIdTextList,
    'keyword_list' => implode(' ', $keywordList)
    ));
    $sql = 'INSERT INTO product_search(category_id, price_from_x_100, value_id_list, keyword_list) VALUES()';
    $this->productSearchOutput []= $sql;
    DbConnection::connect('jingdong');
  }
}