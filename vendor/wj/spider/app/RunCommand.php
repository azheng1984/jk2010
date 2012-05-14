<?php
class RunCommand {
  public function execute($merchant) {
    if (is_dir(CONFIG_PATH.'merchant/'.$merchant) === false) {
      echo 'no merchant "'.$merchant.'"'.PHP_EOL;
      exit;
    }
    DbConnection::connect($merchant);
    Lock::execute();
    TaskCleaner::clean();
    $spider = new Spider;
    $spider->run();
  }
}