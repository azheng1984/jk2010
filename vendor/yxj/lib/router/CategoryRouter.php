<?php
class CategoryRouter {
  public static function execute($id) {
    DbConnection::connect('youxuanji');
    $category = Db::getRow('SELECT * FROM category WHERE id = ?', $id);
    DbConnection::close();
    $GLOBALS['CATEGORY'] = $category;
    return '/category';
  }
}