<?php
class DbPinyin {
  private static $isConnected;

  public static function get($key) {
    self::connect();
    $sql = 'SELECT `value` FROM pinyin WHERE `key` = ?';
    $value = Db::getColumn($sql, $key);
    DbConnection::connect('default');
    return $value;
  }

  public static function insert($key, $value) {
    self::connect();
    $sql = 'INSERT INTO pinyin(key, value) VALUES(?, ?)';
    Db::execute($sql, $key, $value);
    DbConnection::connect('default');
  }

  private static function connect() {
    if (!self::$isConnected) {
      DbConnection::connect(
        'pinyin', new PDO('sqlite:'.ROOT_PATH.'data/pinyin.sqlite')
      );
      self::$isConnected = true;
      return;
    }
    DbConnection::connect('pinyin');
  }
}