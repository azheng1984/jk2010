<?php
class ProcessCommand {
  public function execute() {
    for (;;) {
      $task = Db::getRow(
        'SELECT * FROM task WHERE status != "done"'
          .' ORDER BY id LIMIT 1'
      );
      if ($task !== false && $task['status'] !== 'init') {
        if ($task['status'] !== 'retry') {
          Db::update('task', array('status' => 'retry'), 'id = ?', $task['id']);
        }
        SyncFile::initialize($task);
        $this->sync($task);
        Db::update('task', array('status' => 'done'), 'id = ?', $task['id']);
        SyncFile::remove();
      } else {
        sleep(10);
      }
    }
  }

  private function sync($task) {
    try {
      $syncDb = new SyncDb;
      $syncDb->execute(
        $task['category_id'],
        $task['category_name'],
        $task['status']
      );
      SphinxIndex::index();
      $this->upgradeIndexVersion();
      $syncDb->merge();
      $this->upgradePortalVersion();
    } catch (Exception $exception) {
      DbConnection::closeAll();
      throw $exception;
      sleep(10);
      $task['status'] = 'retry';
      $this->sync($task);
    }
  }

  private function upgradeIndexVersion() {
    file_put_contents(
      DATA_PATH.'version.php', "<?php return array('delta' => true);"
    );
  }

  private function upgradePortalVersion() {
    file_put_contents(
      DATA_PATH.'version.php',
      "<?php return array('delta' => false);"
    );
  }
}