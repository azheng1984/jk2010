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
    $GLOBALS['VERSION'] = $this->getVersion();
    for (;;) {
      try {
        RemoteTask::check();
        $task = $this->getNextTask();
        if ($task === false) {
          sleep(10);
          continue;
        }
        Db::beginTransaction();
        if ($task['category_name'] === '<LAST>') {
          $this->updateVersionInfo('jingdong');
          $this->removeTask($task['id']);
          continue;
        }
        ++$this->remoteTaskIndex;
        file_put_contents(
          DATA_PATH.'remote_task_index.php',
          '<?php return '.$this->remoteTaskIndex.';'
        );
        $isNew = null;
        $shoppingCategoryId = SyncCategory::getCategoryId(
          $task['category_name'], $isNew
        );
        CommandSyncFile::initialize(
          $this->remoteTaskIndex, 1, $shoppingCategoryId, $task['version']
        );
        ImageSyncFile::initialize(
          $this->remoteTaskIndex, 1, $shoppingCategoryId, $task['version']
        );
        if ($isNew) {
          CommandSyncFile::insertCategory($task['category_name']);
        }
        $propertyList = SyncProperty::getPropertyList(
          $task['category_name'], $task['merchant_name'], $task['version']
        );
        SyncProduct::execute(
          $task['category_name'],
          $shoppingCategoryId,
          $propertyList,
          $task['version'],
          $task['merchant_name']
        );
        CommandSyncFile::finalize();
        ImageSyncFile::finalize();
        RemoteTask::add(
          $this->remoteTaskIndex, $shoppingCategoryId,
          $task['category_name'], 1, $task['version']
        );
        $this->removeTask($task['id']);
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

  private function updateVersionInfo($merchantName) {
    $this->versionInfo['merchant'][$merchantName] = true;
    $merchantList = array('jingdong'); //TODO:配置
    $isUpdate = true;
    foreach ($merchantList as $merchant) {
      if (isset($version[$merchant]) === false) {
        $isUpdate = false;
        break;
      }
    }
    if ($isUpdate) {
      $this->versionInfo['version'] = ++$this->versionInfo['version'];
      $this->versionInfo['merchant'] = array();
      ++$GLOBALS['VERSION'];
    }
    file_put_contents(
      DATA_PATH.'version.php',
      '<?php return '.var_export($this->versionInfo, true).';'
    );
  }

  private function getVersion() {
    $this->versionInfo = require DATA_PATH.'version.php';
    return $this->versionInfo['version'];
  }
}