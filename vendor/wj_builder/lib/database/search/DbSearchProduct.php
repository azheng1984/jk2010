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
    $keywordList,
    $valueIdLists
  ) {
    $sql = 'INSERT INTO `wj_search`.`product` (
      `id`,
      `lowest_price_x_100`,
      `discount_x_10`,
      `sale_rank`,
      `publish_timestamp`,
      `category_id`,
      `key_id_list`,
      `keyword_list`,
      `value_id_list_1`,
      `value_id_list_2`,
      `value_id_list_3`,
      `value_id_list_4`,
      `value_id_list_5`,
      `value_id_list_6`,
      `value_id_list_7`,
      `value_id_list_8`,
      `value_id_list_9`,
      `value_id_list_10`
    ) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
    Db::execute($sql,
      $id,
      $lowestPriceX100,
      $discountX10,
      $saleRank,
      $publishTimestamp,
      $categoryId,
      $keyIdList,
      $keywordList,
      $valueIdLists[1],
      $valueIdLists[2],
      $valueIdLists[3],
      $valueIdLists[4],
      $valueIdLists[5],
      $valueIdLists[6],
      $valueIdLists[7],
      $valueIdLists[8],
      $valueIdLists[9],
      $valueIdLists[10]
    );
  }

  public static function update(
    $id,
    $lowestPriceX100,
    $discountX10,
    $saleRank,
    $publishTimestamp,
    $categoryId,
    $keyIdList,
    $keywordList,
    $valueIdLists
  ) {
    $sql = 'UPDATE `wj_search`.`product`'
      .' SET '
      .'`lowest_price_x_100` = ?,'
      .'`discount_x_10` = ?,'
      .'`sale_rank` = ?,'
      .'`publish_timestamp` = ?,'
      .'`category_id` = ?,'
      .'`key_id_list` = ?,'
      .'`keyword_list` = ?,'
      .'`value_id_list_1` = ?,'
      .'`value_id_list_2` = ?,'
      .'`value_id_list_3` = ?,'
      .'`value_id_list_4` = ?,'
      .'`value_id_list_5` = ?,'
      .'`value_id_list_6` = ?,'
      .'`value_id_list_7` = ?,'
      .'`value_id_list_8` = ?,'
      .'`value_id_list_9` = ?,'
      .'`value_id_list_10` = ?,'
      .' WHERE id = ?';
    Db::execute(
      $sql,
      $lowestPriceX100,
      $discountX10,
      $saleRank,
      $publishTimestamp,
      $categoryId,
      $keyIdList,
      $keywordList,
      $valueIdLists[1],
      $valueIdLists[2],
      $valueIdLists[3],
      $valueIdLists[4],
      $valueIdLists[5],
      $valueIdLists[6],
      $valueIdLists[7],
      $valueIdLists[8],
      $valueIdLists[9],
      $valueIdLists[10],
      $id
    );
  }

  public static function updateSaleRank($id, $saleRank) {
    $sql = 'UPDATE `wj_search`.`product` SET `sale_rank` = ? WHERE id = ?';
    Db::execute($sql, $saleRank, $id);
  }

  public static function updatePrice($id, $lowestPriceX100, $discountX10) {
    $sql = 'UPDATE `wj_search`.`product`'
      .' SET `lowest_price_x_100` = ?, `discount_x_10` = ? WHERE id = ?';
    Db::execute($sql, $lowestPriceX100, $discountX10, $id);
  }
}