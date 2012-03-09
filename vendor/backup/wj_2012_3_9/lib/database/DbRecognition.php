<?php
class DbRecognition {
  public static function get($id) {
    return Db::getRow('SELECT * FROM recognition WHERE id = ?', $id);
  }
}