<?php
class RunCommand {
  private $versionInfo;
  private $remoteTaskIndex;

  public function execute() {
    Lock::execute();
    CommandSyncFile::clean();
    ImageSyncFile::clean();
    $this->remoteTaskIndex = require DATA_PATH.'remote_task_index.php';
    Db::execute('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
    for (;;) {
      try {
        RemoteTask::check();
        $task = $this->getNextTask();
        if ($task === false) {
          sleep(10);
          continue;
        }
        Db::beginTransaction();
        ++$this->remoteTaskIndex;
        file_put_contents(
          DATA_PATH.'remote_task_index.php',
          '<?php return '.$this->remoteTaskIndex.';'
        );
        CommandSyncFile::initialize($this->remoteTaskIndex);
        ImageSyncFile::initialize($this->remoteTaskIndex);
        SyncProduct::execute(
          $task['category_id'],
          $task['version'],
          'jingdong'
        );
        CommandSyncFile::finalize();
        ImageSyncFile::finalize();
        RemoteTask::add($this->remoteTaskIndex, 1);
        $this->removeTask($task['id']);
        exit;
        Db::commit();
      } catch (Exception $exception) {
        throw $exception;
        DbConnection::connect();
        Db::rollback();
        DbConnection::closeAll();
        CommandSyncFile::clean();
        ImageSyncFile::clean();
        sleep(10);
      }
    }
  }

  private function getNextTask() {
    return Db::getRow('SELECT * FROM task ORDER BY id LIMIT 1');
  }

  private function removeTask($id) {
    Db::delete('task', 'id = ?', $id);
  }
}