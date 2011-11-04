<?php
class DbProduct {
  public static function get($id) {
    return Db::getRow(
      'SELECT * FROM `jingdong`.`food_product` WHERE id = ?', $id
    );
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
}