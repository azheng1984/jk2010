<?php
class History {
  public static function bind($processor, $path, $status, $categoryId) {
    $history = Db::getRow(
      'SELECT id, category_id, _status FROM history'
        .' WHERE processor = ? AND path = ?',
      $processor, $path
    );
    if ($history === false && $categoryId === null) {
      return;
    }
    if ($history === false) {
      $history = array(
        'processor' => $processor,
        'path' => $path,
        'category_id' => $categoryId,
        '_status' => $status,
        'last_ok_date' => date('Y-m-d'),
        'version' => $GLOBALS['VERSION'],
      );
      Db::insert('history', $history);
      return;
    }
    $replacementColumnList = array(
      'version' => $GLOBALS['VERSION'],
    );
    if ($categoryId !== null
      && intval($history['category_id']) !== $categoryId) {
      $replacementColumnList['category_id'] = $categoryId;
    }
    if ($status !== $history['_status']) {
      $replacementColumnList['_status'] = $status;
    }
    if ($status === 200) {
      $replacementColumnList['last_ok_date'] = date('Y-m-d');
    }
    Db::update('history', $replacementColumnList, 'id = ?', $history['id']);
  }
}