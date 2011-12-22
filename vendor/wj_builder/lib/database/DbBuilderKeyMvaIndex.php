<?php
class DbBuilderKeyMvaIndex {
  public static function getNext($categoryId) {
    $row = self::get($categoryId);
    if ($row === false) {
      self::insert($categoryId);
      return 1;
    }
    Db::execute(
      'UPDATE `wj_builder`.`key_mva_index` SET `index` = ?'
      .' WHERE `category_id` = ?',
      ++$row['index'],
      $categoryId
    );
  }

  private static function get($categoryId) {
    return Db::getRow(
      'SELECT * FROM `wj_builder`.`key_mva_index`'
      .' WHERE category_id = ?', $categoryId
    );
  }

  private static function insert($categoryId) {
    Db::execute(
      'INSERT INTO `wj_builder`.`key_mva_index`(`category_id`) VALUES(?);',
      $categoryId
    );
  }
}