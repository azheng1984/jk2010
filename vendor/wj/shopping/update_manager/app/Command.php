<?php
//TODO:幂等
class Command {
  public function execute() {
    for (;;) {
      $task = Db::getRow('SELECT * FROM task ORDER BY id LIMIT 1');
      if ($task !== false) {
        $this->sync($task);
        Db::delete('task', 'id = ?', $task['id']);
        continue;
      }
      sleep(10);
    }
  }

  private function sync($task) {
    try {
      $fileList = SyncFile::execute($task);
      SyncDb::execute($fileList['portal']);
      SphinxIndex::indexDelta();
      $this->upgradeIndexVersion();
      SyncDb::merge();
      $this->upgradePortalVersion();
      SyncFile::finialize();
    } catch (Exception $exception) {
      $this->sync($task);
    }
  }

  private function upgradeIndexVersion() {
    file_put_contents(
      PORTAL_DATA_PATH.'version.php', "<?php return array('delta' => true);"
    );
  }

  private function upgradePortalVersion() {
    file_put_contents(
      PORTAL_DATA_PATH.'version.php',
      "<?php return array('delta' => false);"
    );
  }
}