<?php
class DbMerchantList {
  public static function getList($typeId, $page) {
    $offset = ($page - 1) * 25;
    $sqlPrefix = 'SELECT merchant.name,merchant_list.path,merchant_list.uri '
      .'FROM merchant_list INNER JOIN merchant ON '
      .'merchant.id = merchant_list.merchant_id';
    $sqlSuffix = ' ORDER BY merchant_list.id LIMIT '.$offset.', 25';
    if ($typeId !== null) {
      return Db::getAll($sqlPrefix.' WHERE type_id = ?'.$sqlSuffix, $typeId);
    }
    return Db::getAll($sqlPrefix.$sqlSuffix);
  }
}