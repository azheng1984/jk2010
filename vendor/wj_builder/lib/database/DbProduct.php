<?php
class DbProduct {
  public static function get($id) {
    return Db::getRow(
      'SELECT * FROM `jingdong`.`food_product` WHERE id = ?', $id
    );
  }

  public static function hasWebImage($webProductid) {
    return Db::getColumn(
      'SELECT image_db_index FROM `wj_web`.`product` WHERE id = ?',
      $webProductid
    ) !== '0';
  }

  public static function updateHasWebImage($webProductId, $hasWebImage) {
    $sql = 'UPDATE `wj_web`.`product` SET `image_db_index` = ? WHERE id = ?';
    Db::execute($sql, $hasWebImage, $webProductId);
  }

  public static function insertIntoWeb(
    $lowestPriceX100,
    $highestPriceX100,
    $cutPriceX100,
    $merchantId,
    $url,
    $imageDbIndex,
    $categoryId,
    $title,
    $description
  ) {
    $sql = 'INSERT INTO `wj_web`.`product` (
      `lowest_price_x_100`,
      `highest_price_x_100`,
      `cut_price_x_100`,
      `merchant_id`,
      `url`,
      `image_db_index`,
      `category_id`,
      `title`,
      `description`
    ) VALUES(?,?,?,?,?,?,?,?,?)';
    Db::execute($sql,
      $lowestPriceX100,
      $highestPriceX100,
      $cutPriceX100,
      $merchantId,
      $url,
      $imageDbIndex,
      $categoryId,
      $title,
      $description
    );
    return DbConnection::get()->lastInsertId();
  }

  public static function insertIntoSearch(
    $id,
    $lowestPriceX100,
    $cutPriceX100,
    $saleRank,
    $categoryId,
    $keyIdList,
    $content
  ) {
    $sql = 'INSERT INTO `wj_search`.`product` (
      `id`,
      `lowest_price_x_100`,
      `cut_price_x_100`,
      `sale_rank`,
      `category_id`,
      `key_id_list`,
      `content`
    ) VALUES(?,?,?,?,?,?,?)';
    Db::execute($sql,
      $id,
      $lowestPriceX100,
      $cutPriceX100,
      $saleRank,
      $categoryId,
      $keyIdList,
      $content
    );
  }

  public static function resetSearchValueIdList($id) {
    for ($index = 0; $index < 10; ++$index) {//TODO:preformance
      ++$index;
      $sql = 'UPDATE `wj_search`.`product`'
        .' SET `value_id_list_'.$index.'` = NULL WHERE id = ?';
      Db::execute($sql, $id);
    }
  }

  public static function updateSearchValueIdList($id, $index, $valueIdList) {
    $sql = 'UPDATE `wj_search`.`product`'
      .' SET `value_id_list_'.$index.'` = ? WHERE id = ?';
    Db::execute($sql, $valueIdList, $id);
  }

  public static function updateWebProductId($id, $webProductId) {
    $sql = 'UPDATE `jingdong`.`food_product` SET web_product_id = ?'
      .' WHERE id = ?';
    Db::execute($sql, $webProductId, $id);
  }

  public static function updateWebPrice(
    $webProductId, $lowestPriceX100, $highestPriceX100
  ) {
    $sql = 'UPDATE `wj_web`.`product`'
      .' SET `lowest_price_x_100` = ?, `highest_price_x_100` = ? WHERE id = ?';
    Db::execute($sql, $lowestPriceX100, $highestPriceX100, $webProductId);
  }

  public static function updateSearchPrice(
    $webProductId, $lowestPriceX100, $cutPriceX100
  ) {
    $sql = 'UPDATE `wj_search`.`product`'
      .' SET `lowest_price_x_100` = ?, `cut_price_x_100` = ? WHERE id = ?';
    Db::execute($sql, $lowestPriceX100, $cutPriceX100, $webProductId);
  }

  public static function updateSearchSaleRank($webProductId, $saleRank) {
    $sql = 'UPDATE `wj_search`.`product` SET `sale_rank` = ? WHERE id = ?';
    Db::execute($sql, $saleRank, $webProductId);
  }

  public static function updateWebContent(
    $webProductId,
    $categoryId,
    $title,
    $description
  ) {
    $sql = 'UPDATE `wj_web`.`product`'
      .' SET `category_id` = ?, `title` = ?,'
      .' `description` = ? WHERE id = ?';
    Db::execute(
      $sql, $categoryId, $title, $description, $webProductId
    );
  }

  public static function updateSearchContent(
    $webProductId,
    $categoryId,
    $keyIdList,
    $content
  ) {
    $sql = 'UPDATE `wj_search`.`product`'
      .' SET `category_id` = ?, `key_id_list` = ?,'
      .' `content` = ? WHERE id = ?';
    Db::execute(
      $sql, $categoryId, $keyIdList,$content, $webProductId
    );
  }
}