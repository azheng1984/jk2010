<?php
abstract class InitCommand {
  public function execute() {
    self::tryCreateGlobalTables();
    Lock::execute();
    if (DbTask::isEmpty() === false || DbTaskRetry::isEmpty() === false) {
      echo 'fail: task/task_retry not empty';
      return;
    }
    foreach ($this->getCategoryListLinks() as $type => $item) {
      foreach ($item as $domain => $pathList) {
        foreach ($pathList as $name => $valueList) {
          $this->tryCreateTablesByCategory($valueList['table_prefix']);
          DbTask::insert($type, array(
            'name' => $name,
            'path' => $valueList['path'],
            'table_prefix' => $valueList['table_prefix'],
            'domain' => $domain,
            'parent_category_id' => 0
          ));
        }
      }
    }
  }

  protected abstract function getCategoryListLinks();

  private function tryCreateGlobalTables() {
    DbCategory::tryCreateTable();
    DbLock::tryCreateTable();
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