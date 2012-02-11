<?php
class DbHomeMerchant {
  public static function getList($typeId, $start = 0) {
    $sqlPrefix = 'SELECT merchant.name,merchant_home.path,merchant_home.uri '
      .'FROM merchant_home LEFT JOIN merchant ON '
      .'merchant.id = merchant_home.merchant_id WHERE ';
    $sqlSuffix = ' ORDER BY merchant_home.id LIMIT '.$start.', 25';
    if ($typeId !== null) {
      return Db::getAll($sqlPrefix.'type_id = ?'.$sqlSuffix, $typeId);
    }
    return Db::getAll($sqlPrefix.'type_id IS NULL'.$sqlSuffix);
  }
}