<?php
class Command {
  public function execute() {
    for (;;) {
      $task = Db::getRow(
        'SELECT * FROM task WHERE status != "done" ORDER BY id LIMIT 1'
      );
      if ($task !== false) {
        if ($task['status'] !== 'retry') {
          Db::update('task', array('status' => 'retry'), 'id = ?', $task['id']);
        }
        $this->sync($task);
        Db::udpate('task', array('status' => 'done'), 'id = ?', $task['id']);
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
        $task['status']
      );
      SphinxIndex::indexDelta();
      $this->upgradeIndexVersion();
      SyncDb::merge();
      $this->upgradePortalVersion();
      SyncFile::finialize();
    } catch (Exception $exception) {
      sleep(10);
      $task['status'] = 'retry';
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