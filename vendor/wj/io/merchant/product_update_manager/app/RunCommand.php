<?php
//TODO 删除 category name 中的 "其它" 前缀
class RunCommand {
  private $versionInfo;

  public function execute() {
    Lock::execute();
    Db::execute('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
    $GLOBALS['VERSION'] = $this->getVersion();
    for (;;) {
      ShoppingRemoteTask::check();
      $task = $this->getNextTask();
      if ($task === false) {
        sleep(10);
        continue;
      }
      Db::beginTransaction();
      if ($task['category_name'] === ' LAST') {
        $this->updateVersionInfo('jingdong');
        $this->removeTask($task['id']);
        continue;
      }
      $isNew = null;
      $shoppingCategoryId = SyncShoppingCategory::getCategoryId(
        $task['category_name'], $isNew
      );
      ShoppingCommandFile::initialize(
        1, $shoppingCategoryId, $task['version']
      );
      SyncShoppingImage::initialize(1, $shoppingCategoryId, $task['version']);
      if ($isNew) {
        ShoppingCommandFile::insertCategory(
          $shoppingCategoryId, $task['category_name']
        );
      }
      $propertyList = SyncShoppingProperty::getPropertyList(
        $task['category_name'], $task['merchant_name'], $task['version']
      );
      SyncShoppingProduct::execute(
        $task['category_name'],
        $shoppingCategoryId,
        $propertyList,
        $task['version'],
        $task['merchant_name']
      );
      ShoppingCommandFile::finalize();
      SyncShoppingImage::finalize();
      ShoppingRemoteTask::add($shoppingCategoryId, 1, $task['version']);
      $this->removeTask($task['id']);
      exit;
      Db::commit();
      //TODO catch > rallback db & clean file
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
      $this->versionInfo['version']['merchant'] = array();
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