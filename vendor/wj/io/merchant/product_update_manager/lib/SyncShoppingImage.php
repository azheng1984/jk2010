<?php
class SyncShoppingImage {
  private function getImagePath($root) {
    $folder = $this->getImageFolder();
    $levelOne = floor($folder / 10000);
    $folder = $root.$levelOne;
    if (is_dir($folder)) {
      mkdir($folder);
    }
    $levelTwo = $folder % 10000;
    $folder = $folder.'/'.$levelTwo;
    if (is_dir($folder)) {
      mkdir($folder);
    }
    return $root;
  }
  
  private function getImageFolder() {
    DbConnection::connect('io_merchant_spider');
    $row = Db::getRow('SELECT * FROM image_folder ORDER BY amount LIMIT 1');
    if ($row === false || $row['amount'] >= 10000) {
      Db::insert('image_folder', array());
      return Db::getLastInsertId();
    }
    Db::update(
    'image_folder', array('amount' => ++$row['amount']), 'id = ?', $row['id']
    );
    return $row['id'];
  }
}