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
    ) !== 0;
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
    $properties,
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
      `properties`,
      `description`
    ) VALUES(?,?,?,?,?,?,?,?,?,?)';
    Db::execute($sql,
      $lowestPriceX100,
      $highestPriceX100,
      $cutPriceX100,
      $merchantId,
      $url,
      $imageDbIndex,
      $categoryId,
      $title,
      $properties,
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
    $propertyIdList,
    $title,
    $properties,
    $description
  ) {
    $sql = 'INSERT INTO `wj_search`.`product` (
      `id`,
      `lowest_price_x_100`,
      `cut_price_x_100`,
      `sale_rank`,
      `category_id`,
      `property_id_list`,
      `title`,
      `description`,
      `properties`
    ) VALUES(?,?,?,?,?,?,?,?,?)';
    Db::execute($sql,
      $id,
      $lowestPriceX100,
      $cutPriceX100,
      $saleRank,
      $categoryId,
      $propertyIdList,
      $title,
      $properties,
      $description
    );
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
}