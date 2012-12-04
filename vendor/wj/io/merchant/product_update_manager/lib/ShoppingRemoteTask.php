<?php
class ShoppingRemoteTask {
  static public function add($categoryId, $merchantId, $version) {
    $remoteTaskList = Db::getAll('SELECT * FROM remote_task');
    $currentRemoteTask = array(
      'merchant_id' => $merchantId,
      'category_id' => $categoryId,
      'version' => $version
    );
    $remoteTaskList[] = $currentRemoteTask;
    try {
      DbConnection::connect('remote');
      foreach ($$remoteTaskList as $remoteTask) {
        Db::insert('task', array(
          'merchant_id' => $remoteTask['merchant_id'],
          'category_id' => $remoteTask['category_id'],
          'version' => $remoteTask['version']
        ));
        DbConnection::connect('default');
        Db::delete('remote_task', 'id = ?', $remoteTask['id']);
        DbConnection::close();
      }
      DbConnection::close();
    } catch (Exception $exception) {
      DbConnection::connect('default');
      Db::insert('remote_task', $currentRemoteTask);
      DbConnection::close();
    }
  }
}