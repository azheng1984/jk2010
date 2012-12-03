<?php
class ShoppingRemoteTask {
  static public function add($categoryId, $merchantId, $version) {
    $remoteTask = array(
      'category_id' => $categoryId,
      'merchant_id' => $merchantId,
      'version' => $version
    );
    try {
      self::retry();
      DbConnection::connect('remote');
      Db::insert('task', $remoteTask);
      DbConnection::close();
    } catch (Exception $exception) {
      DbConnection::connect('default');
      Db::insert('remote_task', $remoteTask);
      DbConnection::close();
    }
  }

  static function retry() {
    $remoteTaskList = Db::getAll('SELECT * FROM remote_task');
    DbConnection::connect('remote');
    foreach ($$remoteTaskList as $remoteTask) {
      Db::insert('task', array(
        'category_id' => $remoteTask['category_id'],
        'merchant_id' => $remoteTask['merchant_id'],
        'version' => $remoteTask['version']
      ));
      DbConnection::connect('default');
      Db::delete('remote_task', 'id = ?', $remoteTask['id']);
      DbConnection::close();
    }
    DbConnection::close();
  }
}