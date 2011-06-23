<?php
class DatabaseConnection {
  private static $connection;

  public function executeNonQuery($sql, $parameters = array()) {
    self::execute($sql, $parameters);
  }

  public function getRow($sql, $parameters = array()) {
    return self::execute($sql, $parameters)->fetch(PDO::FETCH_ASSOC);
  }

  public function getDataset($sql, $parameters = array()) {
    return self::execute($sql, $parameters)->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getLastInsertId() {
    return self::$connection->lastInsertId();
  }

  private function execute($sql, $parameters) {
    $connection = self::getConnection();
    $statement = $connection->prepare($sql);
    $statement->execute($parameters);
    return $statement;
  }

  private function getConnection() {
    if (self::$connection === null) {
      self::$connection = new PDO(
        "mysql:host=localhost;dbname=source_360buy",
        "root",
        "a841107!",
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
      );
      self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return self::$connection;
  }
}