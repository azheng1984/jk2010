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
        foreach ($pathes as $name => $path) {
          DbTask::insert($type, array(
            'name' => $name,
            'path' => $path,
            'domain' => $domain,
            'parent_category_id' => null
          ));
        }
      }
    }
  }

  protected abstract function getCategoryListLinks();
}