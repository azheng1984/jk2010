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
      $fileList = SyncRemoteFile::sync($task);
      $delta = Portal::update($fileList['portal']);
      PortalDelta::update($delta);
      ProductSearch::update($fileList['product_search'], $delta);
      SphinxIndex::update();
      Version::upgradeIndex();
      Portal::merge($delta);
      Version::upgradePortal();
      SyncRemoteFile::finialize();
    } catch (Exception $exception) {
      $this->sync($task);
    }
  }
}