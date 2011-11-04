<?php
class BuildCommand {
  public function execute() {
    $tablePrefix = 'food';
    while (($item = DbProductUpdate::get($tablePrefix)) !== false) {
      $class = 'Product'.ucfirst(strtolower($item['type'])).'Updater';
      $updater = new $class;
      $updater->execute($item);
      exit;
      DbProductUpdate::delete($tablePrefix, $item['id']);
    }
  }
}