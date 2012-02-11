<?php
class DbHomeMerchant {
  public static function getList($typeId) {
    $sql = 'SELECT merchant.name,merchant_home.path,merchant_home.uri '
      .'FROM merchant_home LEFT JOIN merchant ON '
      .'merchant.id = merchant_home.merchant_id WHERE '
      .'type_id = ? ORDER BY merchant_home.id LIMIT 0, 25';
    return Db::getAll($sql, $typeId);
  }
}