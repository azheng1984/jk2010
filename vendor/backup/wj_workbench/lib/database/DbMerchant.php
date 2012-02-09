<?php
class DbMerchant {
  public static function getList($productId) {
    return Db::getAll(
      'SELECT * FROM mobile_phone_merchant m'
      .' LEFT JOIN global_merchant g ON m.merchant_id = g.id'
      .' WHERE product_id = ?', $productId
    );
  }
}