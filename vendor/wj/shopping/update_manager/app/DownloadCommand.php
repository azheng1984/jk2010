<?php
class DownloadCommand {
  public function execute() {
    for (;;) {
      $task = Db::getRow(
        'SELECT * FROM task WHERE status = "init" ORDER BY id LIMIT 1'
      );
      var_dump($task);
      exit;
      if ($task !== false) {
        try {
          SyncFile::initialize($task);
          SyncFile::execute($task);
          Db::update('task', array('status' => 'ready'), 'id = ?', $task['id']);
        } catch (Exception $ex) {}
      }
    }
  }
}