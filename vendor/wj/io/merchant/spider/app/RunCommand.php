<?php
class RunCommand {
  public function execute() {
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
}