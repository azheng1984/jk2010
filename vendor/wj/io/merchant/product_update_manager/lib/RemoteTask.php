<?php
class RemoteTask {
  static public function add($id, $merchantId) {
    $currentRemoteTask = array('id' => $id, 'merchant_id' => $merchantId);
    Db::insert('remote_task', $currentRemoteTask);
  }

  public static function check() {
    try {
      DbConnection::connect('remote');
      $taskList = Db::getAll(
        'SELECT id, status FROM task WHERE status = "done"'
      );
      foreach ($taskList as $task) {
        if ($task['status'] === 'done') {
          DbConnection::connect('default');
          Db::delete('remote_task', 'id = ?', $task['id']);
          DbConnection::close();
          //TODO try backup/delete sync file
          Db::delete('task', 'id = ?', $task['id']);
        }
      }
      DbConnection::close();
      $taskList = Db::getAll(
        'SELECT * FROM remote_task WHERE status = "not_sync" ORDER BY id'
      );
      foreach ($taskList as $task) {
        DbConnection::connect('remote');
        $row = Db::getRow(
          'SELECT id, status FROM task WHERE id = ?', $task['id']
        );
        if ($row === false) {
          Db::insert('task', array(
            'id' => $task['id'],
            'merchant_id' => $task['merchant_id']
          ));
        }
        DbConnection::close();
        Db::update(
          'remote_task', array('status' => 'sync'), 'id = ?', $task['id']
        );
      }
    } catch (Exception $exception) {
      throw $exception;//TODO ignore
    }
  }
}