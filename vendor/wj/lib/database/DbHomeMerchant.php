<?php
class DbHomeMerchant {
  public static function getList($typeId, $start = 0) {
    $sqlPrefix = 'SELECT merchant.name,merchant_home.path,merchant_home.uri '
      .'FROM merchant_home INNER JOIN merchant ON '
      .'merchant.id = merchant_home.merchant_id';
    $sqlSuffix = ' ORDER BY merchant_home.id LIMIT '.$start.', 25';
    if ($typeId !== null) {
      return Db::getAll($sqlPrefix.' WHERE type_id = ?'.$sqlSuffix, $typeId);
    }
    return Db::getAll($sqlPrefix.$sqlSuffix);
  }
}