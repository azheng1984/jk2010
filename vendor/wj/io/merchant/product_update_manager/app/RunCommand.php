<?php
//TODO 修改 所有 table 成 innodb 类型
//TODO 删除 category name 中的 "其它" 前缀
class RunCommand {
  private $versionInfo;

  public function execute() {
    Lock::execute();
    Db::execute('SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED');
    $GLOBALS['VERSION'] = $this->getVersion();
    for (;;) {
      $task = $this->getNextTask();
      if ($task === false) {
        sleep(10);
        continue;
      }
      if ($task['category_name'] === ' LAST') {
        $this->updateVersion('jingdong');
        continue;
      }
      Db::beginTransaction();
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
      Db::rollback();
      exit;
      Db::commit();
    }
  }

  private function getNextTask() {
    return Db::getRow('SELECT * FROM task ORDER BY id LIMIT 1');
  }

  private function removeTask($id) {
    Db::delete('task', $id);
  }

  private function updateVersion($merchantName) {
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