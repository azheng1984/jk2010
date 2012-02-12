<?php
class DbCategory {
  public static function getByName($name) {
    return Db::getRow(
      'SELECT * FROM category WHERE `name` = ?', $name
    );
  }
}