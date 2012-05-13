<?php
abstract class InitCommand {
  public function execute($merchant) {
    self::tryCreateGlobalTables();
    Lock::execute();
    if (Db::getRow('SELECT id FROM task') !== false
      || Db::getRow('SELECT id FROM task_retry') !== false) {
      echo 'fail: task/task_retry not empty';
      return;
    }
    foreach ($this->getCategoryTaskList() as $type => $item) {
      foreach ($item as $domain => $pathList) {
        foreach ($pathList as $name => $valueList) {
          $this->tryCreateTablesByCategory($valueList['table_prefix']);
          $argumentList = array(
            'name' => $name,
            'path' => $valueList['path'],
            'table_prefix' => $valueList['table_prefix'],
            'domain' => $domain
          );
          Db::insert('task', array(
            'type' => $type,
            'argument_list' => var_export($argumentList, true)
          ));
        }
      }
    }
  }

  protected abstract function getCategoryTaskList();

  private function tryCreateGlobalTables() {
    Db::execute(file_get_contents(CONFIG_PATH.'global_table.sql'));
  }

  private function tryCreateTablesByCategory($tablePrefix) {
    $sql = file_get_contents(CONFIG_PATH.'table_by_category.sql');
    $sql = preg_replace('/CREATE TABLE IF NOT EXISTS `(.*?)`/',
      'CREATE TABLE IF NOT EXISTS `'.$tablePrefix.'_$1`', $sql);
    Db::execute($sql);
    ImageDb::tryCreateDb($tablePrefix);
  }
}