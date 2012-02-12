<?php
class Home {
  private $config;
  private $page = null;

  public function __construct() {
    $this->config = require CONFIG_PATH.'home.config.php';
  }

  public function getConfig() {
    return $this->config;
  }

  public function getPage() {
    if ($this->page === null) {
      $this->parsePage();
    }
  }

  private function parsePage() {
    if (!isset($_GET['page'])) {
      return 1;
    }
    if (!is_numeric($_GET['page']) || $_GET['page'] < 1) {
      throw new NotFoundException;
    }
    return intval($_GET['page']);
  }

  public function getMerchantList() {
    
  }
}