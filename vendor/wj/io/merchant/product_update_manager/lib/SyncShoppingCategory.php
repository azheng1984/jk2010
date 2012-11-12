<?php
class SyncShoppingCategory {
  public static function getCategoryId($categoryName) {
    DbConnection::connect('shopping');
    $category = Db::getRow(
      'SELECT id, version FROM category WHERE name = ?', $categoryName
    );
    $id = null;
    if ($category !== false) {
      $id = $category['id'];
      if ($category['version'] !== $GLOBALS['VERSION']) {
        Db::update(
          'category', array('version' => $GLOBALS['VERSION']), 'id = ?', $id
        );
      }
    }
    if ($category === false) {
      Db::insert(
        'category',
        array('name' => $categoryName, 'version' => $GLOBALS['VERSION'])
      );
      $id = Db::getLastInsertId();
      ShoppingCommandFile::insertCategory($id, $categoryName);
    }
    DbConnection::close();
    return $id;
  }
}