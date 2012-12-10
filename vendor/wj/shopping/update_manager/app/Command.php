<?php
class Command {
  public function execute() {
    for (;;) {
      $task = Db::getRow('SELECT * FROM task ORDER BY id LIMIT 1');
      if ($task !== false) {
        if ($task['is_retry'] === '0') {
          Db::update('task', array('is_retry' => 1), 'id = ?', $task['id']);
        }
        $this->sync($task);
        Db::delete('task', 'id = ?', $task['id']);
        continue;
      }
    }
  }

  private function sync($task) {
    try {
      SyncFile::execute($task);
      $syncDb = new SyncDb;
      $syncDb->execute(
        $task['category_id'],
        $task['category_name'],
        $task['is_retry']
      );
      SphinxIndex::indexDelta();
      $this->upgradeIndexVersion();
      SyncDb::merge();
      $this->upgradePortalVersion();
      SyncFile::finialize();
    } catch (Exception $exception) {
      sleep(10);
      $task['is_retry'] = '1';
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