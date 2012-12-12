<?php
class SyncCategory {
  public static function getCategoryId($categoryName, &$isNew) {
    $category = Db::getRow(
      'SELECT id, version FROM category WHERE name = ?', $categoryName
    );
    $id = null;
    $isNew = false;
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
      $isNew = true;
    }
    return $id;
  }
}