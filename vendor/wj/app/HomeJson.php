<?php
class HomeJson extends Json {
  private $page;
  private $merchantIndex;

  public function __construct() {
    $config = require CONFIG_PATH.'home.config.php';
    if (!isset($_GET['page']) || !is_numeric($_GET['page'])
      || $_GET['page'] < 2) {
      throw new NotFoundException;
    }
    $this->page = $_GET['page'];
    if ($GLOBALS['PATH_SECTION_LIST'][1] === '') {
      $this->merchantIndex = null;
      return;
    }
    $path = $GLOBALS['PATH_SECTION_LIST'][1];
    if (!isset($config['merchant_index_list'][$path])) {
      throw new NotFoundException;
    }
    $this->merchantIndex = $config['merchant_index_list'][$path];
  }

  protected function renderJson() {
    $merchantTypeId = null;
    if ($this->merchantIndex !== null) {
      $merchantTypeId = $this->merchantIndex[0];
    }
    $start = ($this->page - 1) * 25;
    $list = DbHomeMerchant::getList($merchantTypeId, $start);
    $buffer = array();
    foreach ($list as $item) {
      $buffer[] = '{"name":"'.$item['name'].'","uri":"'.$item['uri']
        .'","path":"'.$item['path'].'"}';
    }
    echo '[', implode(',', $buffer), ']';
  }
}