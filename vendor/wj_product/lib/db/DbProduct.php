<?php
class DbProduct {
  public static function get($id) {
    return Db::getRow('SELECT * FROM global_product_index WHERE id = ?', $id);
  }
}