<?php
class DbWebProduct {
  public static function insert(
    $lowestPriceX100,
    $highestPriceX100,
    $listLowestPriceX100,
    $merchantId,
    $imageDbIndex,
    $categoryId,
    $uri,
    $title,
    $description
  ) {
    $sql = 'INSERT INTO `wj_web`.`product` (
      `lowest_price_x_100`,
      `highest_price_x_100`,
      `list_lowest_price_x_100`,
      `merchant_id`,
      `uri`,
      `image_db_index`,
      `category_id`,
      `title`,
      `description`
    ) VALUES(?,?,?,?,?,?,?,?,?)';
    Db::execute($sql,
      $lowestPriceX100,
      $highestPriceX100,
      $listLowestPriceX100,
      $merchantId,
      $uri,
      $imageDbIndex,
      $categoryId,
      $title,
      $description
    );
    return DbConnection::get()->lastInsertId();
  }

  public static function update(
    $webProductId,
    $lowestPriceX100,
    $highestPriceX100,
    $listLowestPriceX100,
    $imageDbIndex,
    $merchantId,
    $categoryId,
    $uri,
    $title,
    $description
  ) {
    $sql = 'UPDATE `wj_web`.`product` SET '
      .'`lowest_price_x_100 = ?,'
      .'`highest_price_x_100 = ?,'
      .'`list_lowest_price_x_100 = ?,'
      .'`image_db_index = ?,'
      .'`merchant_id = ?,'
      .'`category_id` = ?,'
      .'`uri = ?,'
      .'`title` = ?,'
      .'`description` = ?'
      .' WHERE id = ?';
    Db::execute($sql,
      $lowestPriceX100,
      $highestPriceX100,
      $listLowestPriceX100,
      $imageDbIndex,
      $merchantId,
      $categoryId,
      $uri,
      $title,
      $description,
      $webProductId
    );
  }
}