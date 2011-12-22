<?php
class DbSearchProduct {
  public static function insert(
    $id,
    $lowestPriceX100,
    $discountX10,
    $saleRank,
    $categoryId,
    $publishTimestamp,
    $keyIdList,
    $keywordList
  ) {
    $sql = 'INSERT INTO `wj_search`.`product` (
      `id`,
      `lowest_price_x_100`,
      `discount_x_10`,
      `sale_rank`,
      `publish_timestamp`,
      `category_id`,
      `key_id_list`,
      `keywords`
    ) VALUES(?,?,?,?,?,?,?)';
    Db::execute($sql,
      $id,
      $lowestPriceX100,
      $discountX10,
      $saleRank,
      $publishTimestamp,
      $categoryId,
      $keyIdList,
      $keywordList
    );
  }
}