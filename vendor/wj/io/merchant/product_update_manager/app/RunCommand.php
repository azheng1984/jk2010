<?php
class RunCommand {
  public function execute() {
    Lock::execute();
    for (;;) {
      $task = $this->getNextTask();
      DbConnection::connect($task['merchant_name']);
      $categoryId = SyncShoppingCategory::getCategoryId($task['category_name']);
      $propertyList = SyncShoppingProperty::getPropertyList($categoryId);
      SyncShoppingProduct::execute($categoryId, $propertyList);
      DbConnection::close();
      ShoppingCommandFile::finalize($categoryId);
      ShoppingImageFolder::finalize($categoryId);
      ShoppingRemoteTask::add($task);
      $this->removeTask($task['id']);
      if ($task['is_last'] === '1') {
        //TODO:更新整体版本完成度
      }
    }
  }

  private function getNextTask() {
  }

  private function removeTask($id) {
  }
}