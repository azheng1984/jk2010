<?php
class BuildCommand {
  public function execute() {
    $tablePrefix = 'food';
    while (($item = DbSpiderProductLog::get($tablePrefix)) !== false) {
      $class = 'Product'.ucfirst(strtolower($item['type'])).'Processor';
      $processor = new $class;
      $processor->execute($item);
      DbSpiderProductLog::delete($tablePrefix, $item['id']);
      echo '.';
    }
  }
}