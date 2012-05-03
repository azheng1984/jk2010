<?php
abstract class InitCommand {
  public function execute() {
    self::tryCreateGlobalTables();
    Lock::execute();
    if (Db::getRow('SELECT id FROM task') === false
      || Db::getRow('SELECT id FROM task_retry') === false) {
      echo 'fail: task/task_retry not empty';
      return;
    }
    foreach ($this->getCategoryListLinks() as $type => $item) {
      foreach ($item as $domain => $pathList) {
        foreach ($pathList as $name => $valueList) {
          $this->tryCreateTablesByCategory($valueList['table_prefix']);
          $argumentList = array(
            'name' => $name,
            'path' => $valueList['path'],
            'table_prefix' => $valueList['table_prefix'],
            'domain' => $domain,
            'parent_category_id' => 0
          );
          Db::insert('task', array(
            'type' => $type,
            'argument_list' => var_export($argumentList, true)
          ));
        }
      }
    }
  }

  protected abstract function getCategoryListLinks();

  private function tryCreateGlobalTables() {
    Db::execute(file_get_contents(CONFIG_PATH.'global_table.sql'));
  }

  private function tryCreateTablesByCategory($tablePrefix) {
    $sql = file_get_contents(CONFIG_PATH.'table_by_category.sql');
    preg_replace('/CREATE TABLE IF NOT EXISTS `(.*?)`/',
      'CREATE TABLE IF NOT EXISTS `'.$tablePrefix.'_$1`', $sql);
    DbImage::tryCreateDb($tablePrefix);
  }

  private function dropTablesByCategory($tablePrefix) {
    Db::execute('DROP TABLE '.$tablePrefix.'_product'); 
    Db::execute('DROP TABLE '.$tablePrefix.'_log');
    /* delete image db manually */
  }
}