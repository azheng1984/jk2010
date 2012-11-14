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
      $fileList = SyncFile::sync($task);
      SyncDb::update($fileList['portal']);
      SphinxIndex::update();
      $this->upgradeIndexVersion();
      SyncDb::merge();
      $this->upgradePortalVersion();
      SyncFile::finialize();
    } catch (Exception $exception) {
      $this->sync($task);
    }
  }

  private function upgradeIndexVersion() {
    
  }

  private function upgradePortalVersion() {
    
  }
}