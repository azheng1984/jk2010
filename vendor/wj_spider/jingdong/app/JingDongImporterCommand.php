<?php
class JingDongImporterCommand {
  public function execute() {
    $sql = 'SELECT * FROM Product';
    foreach (Db::getAll($sql) as $product) {
      
    }
  }
}