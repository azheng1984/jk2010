<?php
//TODO 识别 html 是否被重定向到首页（如果是，暂停后重复链接三次）
//TODO 记录抓取错误日志（考虑使用 webclient 钩子）
class RunCommand {
  public function execute($matchErrorLogId = null) {
    if ($matchErrorLogId !== null) {
      $this->exportMatchErrorLog($matchErrorLogId);
      return;
    }
    Lock::execute();
    for (;;) {
      $GLOBALS['VERSION'] = $this->getVersion();
      $processor = new JingdongCategoryListProcessor;
      $processor->execute();
      $this->cleanPropertyKey();
      $this->cleanPropertyValue();
      $this->cleanHistory();
      JingdongProductListProcessor::finalize();
      JingdongPropertyProductListProcessor::finalize();
      JingdongProductProcessor::finalize();
      $this->upgradeVersion();
    }
  }

  private function getVersion() {
    return intval(file_get_contents(ROOT_PATH.'data/version'));
  }

  private function cleanHistory() {
    Db::execute("DELETE FROM history WHERE last_ok_date < '"
      .date('Y-m-d', time() - (100 * 24 * 60 * 60)).'" OR _status = 404');
  }

  private function upgradeVersion() {
    file_put_contents(ROOT_PATH.'data/version', ++$GLOBALS['VERSION']);
  }

  private function cleanPropertyKey() {
    Db::execute(
      'DELETE FROM property_key WHERE version != ?', $GLOBALS['VERSION']
    );
  }

  private function cleanPropertyValue() {
    Db::execute(
      'DELETE FROM property_value WHERE version != ?', $GLOBALS['VERSION']
    );
  }

  private function exportMatchErrorLog($id) {
    $log = Db::getRow('SELECT * FROM match_error_log WHERE id = ?', $id);
    if ($log === false) {
      return;
    }
    file_put_contents(
      '/home/azheng/Desktop/'.$id.'.html', gzuncompress($log['document'])
    );
    unset($log['document']);
    print_r($log);
  }
}