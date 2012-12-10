<?php
class ShoppingRemoteTask {
  static public function add($categoryId, $merchantId, $version) {
    $currentRemoteTask = array(
      'merchant_id' => $merchantId,
      'category_id' => $categoryId,
      'version' => $version
    );
    Db::insert('remote_task', $currentRemoteTask);
  }

  public static function check() {
    try {
      DbConnection::connect('remote');
      $taskList = Db::getAll('SELECT * FROM task WHERE status = "done"');
      foreach ($taskList as $task) {
        if ($task['status'] === 'done') {
          DbConnection::connect('default');
          Db::delete('DELETE FROM remote_task WHERE id = ?', $task['id']);
          DbConnection::close();
          //TODO try backup/delete sync file
          Db::delete('task', 'id = ?', $task['id']);
        }
      }
      DbConnection::close();
      $taskList = Db::getAll(
        'SELECT * FROM remote_task WHERE status = "not_ready" ORDER BY id'
      );
      foreach ($taskList as $task) {
        DbConnection::connect('remote');
        $row = Db::getRow('SELECT id, status FROM task WHERE id = ?');
        if ($row === false) {
          Db::insert('task', array(
            'id' => $task['id'],
            'merchant_id' => $task['merchant_id'],
            'category_id' => $task['category_id'],
            'version' => $task['version'],
          ));
        }
        DbConnection::close();
        Db::update(
          'remote_task', array('status' => 'ready'), 'id = ?', $task['id']
        );
      }
    } catch (Exception $exception) {}
  }
}