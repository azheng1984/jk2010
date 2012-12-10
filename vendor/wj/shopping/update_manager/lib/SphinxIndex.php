<?php
class SphinxIndex {
  public static function indexDelta() {
    DbConnection::connect('product_search');
    for (;;) {
      Db::execute('LOCK TABLES indexer_status WRITE');
      $status = Db::getColumn(
        "SELECT status FROM indexer_status WHERE name = 'Main'"
      );
      if ($status !== 'Running') {
        Db::update(
          'indexer_status', array('_status' => 'Running'), "name = 'Delta'"
        );
        Db::execute('UNLOCK TABLES');
        break;
      }
      Db::execute('UNLOCK TABLES');
      sleep(10);
    }
    self::system('indexer delta --config sphinx.conf');
    Db::update(
      'indexer_status', array('_status' => 'OK'), "name = 'Delta'"
    );
    DbConnection::close();
  }

  public static function system($command) {
    $return = null;
    system($command, $return);
    if ($return !== 0) {
      throw new Exception;
    }
  }
}