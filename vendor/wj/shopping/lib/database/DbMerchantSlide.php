<?php
class DbMerchantSlide {
  public static function getList($typeId, $page) {
    $offset = ($page - 1) * 5;
    $sqlPrefix = 'SELECT * FROM merchant_slide ';
    $sqlSuffix = ' ORDER BY id LIMIT '.$offset.', 5';
    if ($typeId !== null) {
      return Db::getAll($sqlPrefix.' WHERE type_id = ?'.$sqlSuffix, $typeId);
    }
    return Db::getAll($sqlPrefix.$sqlSuffix);
  }
}