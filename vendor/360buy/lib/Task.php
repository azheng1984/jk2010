<?php
class Task extends Db {
  private $current = null;

  public function get() {
    return $this->current;
  }

  public function moveToNext() {
    $sql = "select * from task limit 1";
    $connection = new DbConnection;
    $result = $connection->getRow($sql);
    if ($result === false) {
      $this->current = null;
      return false;
    }
    $this->current = $result;
    return true;
  }

  public function add($type, $domain, $path) {
    $sql = "insert into task(type, domain, path)"
      ." values('$type', '$domain', '$path')";
    $connection = new DbConnection;
    $connection->executeNonQuery($sql);
  }

  public function isEmpty() {
    $sql = "select count(*) from task";
    $connection = new DbConnection;
    $result = $connection->getRow($sql);
    if ($result[0] === 0) {
      return true;
    }
    return false;
  }
}