<?php
class DocumentDb {
  private $index;

  public function __construct($index) {
    $this->index = $index;
  }

  public function getConnection() {
    $info = $_ENV['document_database'][$this->index];
    return new PDO("mysql:host={$info[0]};dbname={$info[1]}",
                   $info[2],
                   $info[3],
                   array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
  }
}