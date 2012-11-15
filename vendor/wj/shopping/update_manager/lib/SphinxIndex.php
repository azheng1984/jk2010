<?php
class SphinxIndex {
  public static function indexDelta() {
    DbConnection::connect('product_search');
    $status = Db::getColumn(
      "SELECT status FROM indexer_status WHERE name = 'Main'"
    );
    if ($status !== 'Runing') {
      Db::update(
        'indexer_status', array('_status' => 'Running'), "name = 'Delta'"
      );
      system('indexer delta --config sphinx.conf');
      Db::update(
        'indexer_status', array('_status' => 'OK'), "name = 'Delta'"
      );
    }
    $status = Db::getColumn(
      "SELECT status FROM indexer_status WHERE name = 'Main'"
    );
    if ($status === 'Waiting') {
      system('indexer main --config sphinx.conf');
    }
    Db::update(
      'indexer_status', array('_status' => 'OK'), "name = 'Main'"
    );
    DbConnection::close();
  }

  public static function indexMain() {
    DbConnection::connect('product_search');
    $status = Db::getColumn(
      "SELECT status FROM indexer_status WHERE name = 'Delta'"
    );
    if ($status !== 'Runing') {
      Db::update(
        'indexer_status', array('_status' => 'Running'), "name = 'Main'"
      );
      system('indexer main --config sphinx.conf');
      Db::update(
        'indexer_status', array('_status' => 'OK'), "name = 'Main'"
      );
    }
    $status = Db::getColumn(
      "SELECT status FROM indexer_status WHERE name = 'Delta'"
    );
    if ($status === 'Waiting') {
      system('indexer delta --config sphinx.conf');
    }
    Db::update(
      'indexer_status', array('_status' => 'OK'), "name = 'Delta'"
    );
    DbConnection::close();
  }
}