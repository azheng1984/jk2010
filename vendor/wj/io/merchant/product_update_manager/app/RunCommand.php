<?php
class RunCommand {
  public function execute() {
    Lock::execute();
    for (;;) {
      $task = $this->getNextTask();
      $categoryId = SyncShoppingCategory::getCategoryId($task['category_name']);
      $propertyList = SyncShoppingProperty::getPropertyList($categoryId);
      SyncShoppingProduct::execute($categoryId);
      ShoppingCommandFile::finalize($categoryId);
      SyncShoppingImage::finalize($categoryId);
      ShoppingRemoteTask::notify($task);
      $this->removeTask($task['id']);
    }
  }

  private function getNextTask() {
  }

  private function removeTask($id) {
  }
}