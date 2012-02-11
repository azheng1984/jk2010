<?php
class HomeScreen extends Screen {
  private $config;
  private $merchantList;
  private $merchantIndexName;
  private $merchantIndex;

  public function __construct() {
    $this->config = require CONFIG_PATH.'home.config.php';
    if ($GLOBALS['PATH_SECTION_LIST'][1] === '') {
      $this->merchantList = $this->config['merchant_list'];
      $this->merchantIndexName = null;
      $this->merchantIndex = null;
      return;
    }
    $this->merchantIndexName = $GLOBALS['PATH_SECTION_LIST'][1];
    if (!isset(
      $this->config['merchant_index_list'][$this->merchantIndexName]
    )) {
      throw new NotFoundException;
    }
    $this->merchantIndex
      = $this->config['merchant_index_list'][$this->merchantIndexName];
  }

  protected function renderHtmlHeadContent() {
    echo '<title>货比万家</title>',
    '<meta name="description" content="货比万家购物搜索引擎，',
    '商品信息100%来自公司经营（B2C）的正规商店-网上购物，货比万家！"/>';
    $this->addCssLink('home');
    $this->addJsLink('home');
  }

  protected function renderHtmlBodyContent() {
    if ($this->merchantIndexName !== null) {
      $this->addJs('merchant_amount='.$this->merchantIndex[2].';');
    }
  }
}