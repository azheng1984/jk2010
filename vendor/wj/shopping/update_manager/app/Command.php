<?php
//TODO:幂等
//重建索引时，暂停增量索引，在主索引更新后统一 merge
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
    
  }

  private function upgradePortalVersion() {
    
  }
}