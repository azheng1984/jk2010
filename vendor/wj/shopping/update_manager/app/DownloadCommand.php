<?php
class DownloadCommand {
  public function execute() {
    for (;;) {
      $task = Db::getRow(
        'SELECT * FROM task WHERE status = "init" ORDER BY id LIMIT 1'
      );
      if ($task !== false) {
//        echo 'start'.PHP_EOL;
//        var_dump($task);
        try {
          SyncFile::initialize($task);
          SyncFile::execute($task);
          Db::update('task', array('status' => 'ready'), 'id = ?', $task['id']);
        } catch (Exception $ex) {
          error_log(
            var_export($ex, true),
            0,
            '/home/azheng/Desktop/home/'
              .'shopping_update_manager_download_error.log'
          );
        }
//        echo 'end'.PHP_EOL;
      } else {
//        echo 'sleeping'.PHP_EOL;
        sleep(10);
      }
    }
  }
}