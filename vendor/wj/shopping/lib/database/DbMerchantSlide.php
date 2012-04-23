<?php
class DbMerchantSlide {
  public static function getList($typeId, $page, $itemsPerPage = 5) {
    $offset = ($page - 1) * $itemsPerPage;
    $sql = 'SELECT * FROM merchant_slide';
    $sqlSuffix = ' ORDER BY id LIMIT '.$offset.', '.$itemsPerPage;
    if ($typeId !== null) {
      return Db::getAll($sql.' WHERE type_id = ?'.$sqlSuffix, $typeId);
    }
    return Db::getAll($sql.$sqlSuffix);
  }
}