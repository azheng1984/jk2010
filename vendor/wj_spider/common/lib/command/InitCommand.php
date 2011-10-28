<?php
abstract class InitCommand {
  public function execute() {
    Lock::execute();
    if (!DbTask::isEmpty() || !DbTaskRetry::isEmpty()) {
      echo 'fail:task not empty';
      return;
    }
    foreach ($this->getCategoryListLinks() as $type => $item) {
      foreach ($item as $domain => $pathes) {
        foreach ($pathes as $name => $values) {
          DbTask::insert($type, array(
            'name' => $name,
            'path' => $values['path'],
            'table_prefix' => $values['table_prefix'],
            'domain' => $domain,
            'parent_category_id' => 0
          ));
        }
      }
    }
  }

  protected abstract function getCategoryListLinks();
}