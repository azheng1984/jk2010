<?php
//TODO:幂等
class RunCommand {
  private $versionInfo;

  public function execute() {
    Lock::execute();
    $GLOBALS['VERSION'] = $this->getVersion();
    for (;;) {
      $task = $this->getNextTask();
      DbConnection::connect($task['merchant_name']);
      $shoppingCategoryId = SyncShoppingCategory::getCategoryId(
        $task['category_name']
      );
      $categoryId = Db::getColumn(
        'SELECT id FROM category WHERE name = ?', $task['category_name']
      );
      $propertyList = SyncShoppingProperty::getPropertyList($categoryId);
      SyncShoppingProduct::execute(
        $task['category_name'],
        $shoppingCategoryId,
        $propertyList,
        $task['version'],
        $task['merchant_name']
      );
      DbConnection::close();
      ShoppingCommandFile::finalize($shoppingCategoryId);
      SyncShoppingImage::finalize($shoppingCategoryId);
      ShoppingRemoteTask::add($task);
      DbConnection::connect('default');
      $this->removeTask($task['id']);
      if ($task['is_last'] === '1') {
        $this->updateVersion();
      }
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