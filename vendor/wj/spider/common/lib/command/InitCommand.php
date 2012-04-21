<?php
abstract class InitCommand {
  public function execute() {
    Lock::execute();
    if (!DbTask::isEmpty() || !DbTaskRetry::isEmpty()) {
      echo 'fail:task not empty';
      return;
    }
    foreach ($this->getCategoryListLinks() as $type => $item) {
      foreach ($item as $domain => $pathList) {
        foreach ($pathList as $name => $valueList) {
          $this->createTables($valueList['table_prefix']);
          //$this->resetTables($values['table_prefix']);
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

  private function createTables($tablePrefix) {
    DbProduct::createTable($tablePrefix);
    DbProductLog::createTable($tablePrefix);
    DbProperty::createTable($tablePrefix);
    DbImage::createTable($tablePrefix);
    DbProductProperty::createTable($tablePrefix);
  }

    private function resetTables($tablePrefix) {
    DbProduct::expireAll($tablePrefix);
    DbProductProperty::expireAll($tablePrefix);
    DbProperty::expireAll($tablePrefix);
  }

  private function dropTable($tablePrefix) {
   Db::execute('drop table '.$tablePrefix.'_product');
   Db::execute('drop table '.$tablePrefix.'_product_property');
   Db::execute('drop table '.$tablePrefix.'_property_key');
   Db::execute('drop table '.$tablePrefix.'_property_value');
   Db::execute('drop table '.$tablePrefix.'_product_update');
  }
}