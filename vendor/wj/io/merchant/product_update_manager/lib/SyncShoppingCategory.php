<?php
class SyncShoppingCategory {
  public static function getCategoryId($categoryName) {
    DbConnection::connect('shopping');
    $id = Db::getColumn(
      'SELECT id FROM category WHERE name = ?', $categoryName
    );
    if ($id === false) {
      Db::execute('INSERT INTO category(name) VALUES(?)', $categoryName);
      $id = Db::getLastInsertId();
      ShoppingCommandFile::insertCategory($id, $categoryName);
    }
    DbConnection::close();
    return $id;
  }
}