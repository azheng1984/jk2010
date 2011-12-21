<?php
class DbCategoryKeyCount {
  public static function getCount($categoryId) {
    return Db::getColumn(
      'SELECT `count` FROM `wj_search`.`category_key_count`'
      .' WHERE category_id = ?', $categoryId
    );
  }

  public static function moveNext($categoryId, $count) {
    Db::execute(
      'UPDATE `wj_search`.`category_key_count` SET `count` = ?'
      .' WHERE `category_id` = ?',
      $count,
      $categoryId
    );
  }

  public static function insert($categoryId) {
    Db::execute(
      'INSERT INTO `wj_search`.`category_key_count`(`category_id`) VALUES(?);',
      $categoryId
    );
  }
}