<?php
class DbBuilderKeyMvaIndex {
  public static function getNext($categoryId) {
    $index = self::get($categoryId);
    if ($index === false) {
      self::insert($categoryId);
      return 1;
    }
    Db::execute(
      'UPDATE `wj_builder`.`key_mva_index` SET `index` = ?'
      .' WHERE `category_id` = ?',
      ++$index,
      $categoryId
    );
    return $index;
  }

  public static function get($categoryId) {
    return Db::getColumn(
      'SELECT `index` FROM `wj_builder`.`key_mva_index`'
      .' WHERE category_id = ?', $categoryId
    );
  }

  private static function insert($categoryId) {
    Db::execute(
      'INSERT INTO `wj_builder`.`key_mva_index`(`category_id`) VALUES(?)',
      $categoryId
    );
  }
}