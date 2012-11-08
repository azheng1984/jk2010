<?php
class SyncShoppingCategory {
  public static function getCategoryId($categoryName) {
    DbConnection::connect('shopping');
    $id = Db::getColumn(
      'SELECT id FROM category WHERE name = ?', $categoryName
    );
    if ($id === false) {
      try {
        Db::execute('INSERT INTO category(name) VALUES(?)', $categoryName);
        $id = Db::getLastInsertId();
        ShoppingSqlFile::insertCategory($id, $categoryName);
      } catch(PDOException $exception) {
        $id = Db::getColumn(
          'SELECT id FROM category WHERE name = ?', $categoryName
        );
        if ($id === false) {
          throw $exception;
        }
      }
    }
    DbConnection::close();
    return $id;
  }
}