<?php
class BuildCommand {
  public function execute() {
    $tablePrefix = 'food';
    while (($item = DbSpiderProductLog::get($tablePrefix)) !== false) {
      $class = 'Product'.ucfirst(strtolower($item['type'])).'Builder';
      $builder = new $class;
      $builder->execute($item);
      DbSpiderProductLog::delete($tablePrefix, $item['id']);
      echo '.';
      exit;
    }
  }
}