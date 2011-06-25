<?php
class Task extends Db {
  private $current;

  public function get() {
    return $this->current;
  }

  public function moveToNext() {
    $sql = "select * from task order by id desc limit 1";
    $connection = new DbConnection;
    $result = $connection->getRow($sql);
    if ($result === false) {
      $this->current = null;
      return false;
    }
    $this->current = $result;
    return true;
  }

  public function add($type, $domain, $path, $content = null) {
    $sql = "insert into task(type, domain, path, content)"
      ." values('$type', '$domain', '$path', ?)";
    $connection = new DbConnection;
    $connection->executeNonQuery($sql, array($content));
  }

  public function remove($id) {
    $sql = "delete from task where id=$id";
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