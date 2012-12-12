<?php
class ImageDb {
  public static function get($categoryId, $productId) {
    DbConnection::connect('jingdong_image');
    $image = Db::getColumn($categoryId, array('product_id' => $productId));
    DbConnection::close();
    return $image;
  }
}