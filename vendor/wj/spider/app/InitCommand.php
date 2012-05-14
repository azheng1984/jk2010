x<?php
class InitCommand {
  public function execute($merchant) {
    if (is_dir(CONFIG_PATH.'merchant/'.$merchant) === false) {
      echo 'no merchant "'.$merchant.'"'.PHP_EOL;
      exit;
    }
    DbConnection::connect($merchant);
    $GLOBALS['MERCHANT'] = $merchant;
    self::tryCreateGlobalTables();
    Lock::execute();
    if (Db::getRow('SELECT id FROM task') !== false
      || Db::getRow('SELECT task_id FROM task_retry') !== false) {
      echo 'fail: task/task_retry not empty';
      return;
    }
    foreach ($this->getCategoryTaskList() as $processor => $item) {
      foreach ($item as $domain => $pathList) {
        foreach ($pathList as $name => $valueList) {
          $this->tryCreateTablesByCategory($valueList['table_prefix']);
          $tablePrefix = $valueList[0];
          $path = $valueList[1];
          Db::insert('task', array(
            'processor' => $processor,
            'argument_list' => var_export(array(
              $tablePrefix, $name, $domain, $path
            ), true)
          ));
        }
      }
    }
  }

  private function getCategoryTaskList() {
    return
      require CONFIG_PATH.'merchant/'.$GLOBALS['MERCHANT'].'/init.config.php';
  }

  private function tryCreateGlobalTables() {
    Db::execute(file_get_contents(CONFIG_PATH.'database/global_table.sql'));
  }

  private function tryCreateTablesByCategory($tablePrefix) {
    $sql = file_get_contents(CONFIG_PATH.'database/table_by_category.sql');
    $sql = preg_replace('/CREATE TABLE IF NOT EXISTS `(.*?)`/',
      'CREATE TABLE IF NOT EXISTS `'.$tablePrefix.'_$1`', $sql);
    Db::execute($sql);
    ImageDb::tryCreateDb($tablePrefix);
  }
}