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
    DbCategory::tryCreateTable();
    DbProcessLock::tryCreateTable();
    DbTask::tryCreateTable();
    DbTaskRecord::tryCreateTable();
    DbTaskRetry::tryCreateTable();
  }

  private function tryCreateTablesByCategory($tablePrefix) {
    DbProduct::tryCreateTable($tablePrefix);
    DbLog::tryCreateTable($tablePrefix);
    DbImage::tryCreateTable($tablePrefix);
  }

  private function dropTablesByCategory($tablePrefix) {
    Db::execute('drop table '.$tablePrefix.'_product');
    Db::execute('drop table '.$tablePrefix.'_log');
    /* delete image db manually */
  }
}