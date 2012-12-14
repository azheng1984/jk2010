<?php
class History {
  public static function bind(
    $processor, $path, $status, $categoryId, $oldHistory = null
  ) {
    if ($oldHistory === null) {
      $oldHistory = Db::getRow(
        'SELECT id, category_id, status FROM history'
          .' WHERE processor = ? AND path = ?',
        $processor, $path
      );
    }
    if ($oldHistory === false && $categoryId === null) {
      return;
    }
    if ($oldHistory === false) {
      $history = array(
        'processor' => $processor,
        'path' => $path,
        'category_id' => $categoryId,
        'status' => $status,
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
      && intval($oldHistory['category_id']) !== $categoryId) {
      $replacementColumnList['category_id'] = $categoryId;
    }
    if ($status !== $oldHistory['status']) {
      $replacementColumnList['status'] = $status;
    }
    if ($status === 200) {
      $replacementColumnList['last_ok_date'] = date('Y-m-d');
    }
    Db::update('history', $replacementColumnList, 'id = ?', $oldHistory['id']);
  }
}