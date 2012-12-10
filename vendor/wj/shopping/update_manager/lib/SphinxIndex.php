<?php
class SphinxIndex {
  public static function indexDelta() {
    DbConnection::connect('search');
    for (;;) {
      Db::execute('LOCK TABLES indexer_status WRITE');
      $status = Db::getColumn(
        "SELECT status FROM indexer_status WHERE name = 'main'"
      );
      if ($status !== 'running') {
        Db::update(
          'indexer_status', array('status' => 'running'), "name = 'delta'"
        );
        Db::execute('UNLOCK TABLES');
        break;
      }
      Db::execute('UNLOCK TABLES');
      sleep(10);
    }
    self::system('indexer delta --config sphinx.conf');
    Db::update(
      'indexer_status', array('status' => 'ok'), "name = 'delta'"
    );
    DbConnection::close();
  }

  public static function system($command) {
    $return = null;
    system($command, $return);
    if ($return !== 0) {
      Db::update(
        'indexer_status', array('status' => 'ok'), "name = 'delta'"
      );
      DbConnection::close();
      throw new Exception;
    }
  }
}