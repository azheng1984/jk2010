<?php
class JingdongPropertyProductListProcessor {
  private $html;
  private $valueId;
  private $categoryId;

  public function __construct($categoryId = null, $valueId = null) {
    $this->categoryId = $categoryId;
    $this->valueId = $valueId;
  }

  public function execute($path) {
    $status = 200;
    try {
      $result = WebClient::get('www.360buy.com', '/products/'.$path.'.html');
      $this->html = $result['content'];
      if ($this->valueId === null) {
        $this->valueId = $this->getValueId();
      }
      $this->parseProductList();
      $this->parseNextPage();
    } catch(Exception $exception) {
      throw $exception;
      $status = $exception->getCode();
    }
    $replacementColumnList = array(
      'category_id' => $this->categoryId,
      '_status' => $status,
      'version' => $GLOBALS['VERSION']
    );
    if ($status === 200) {
      $replacementColumnList['last_ok_date'] = date('Y-m-d');
    }
    Db::bind('history', array(
      'processor' => 'PropertyProductList',
      'path' => $path,
    ), $replacementColumnList);
  }

  private function getValueId() {
    //TODO:缓存属性列表
    preg_match(
      "{<dt>(.*?)</dt><dd><div class='content'>.*?class=\"curr\">(.*?)</a>}",
      $this->html, $matches
    );
    if (count($matches) !== 3) {
      throw new Exception(null, 500);
    }
    $keyName = iconv('gbk', 'utf-8', $matches[1]);
    if ($this->categoryId === null) {
      $this->categoryId = $this->getCategoryId();
    }
    $keyId = null;
    Db::bind(
      'property_key',
      array(
        'category_id' => $this->categoryId,
        'name' => str_replace('：', '', $keyName)
      ),
      array('version' => $GLOBALS['VERSION']),
      $keyId
    );
    $valueName = iconv('gbk', 'utf-8', $matches[2]);
    $valueId = null;
    Db::bind(
      'property_value',
      array('key_id' => $keyId, 'name' => $valueName),
      array('version' => $GLOBALS['VERSION']),
      $valueId
    );
    return $valueId;
  }

  private function getCategoryId() {
    preg_match(
      '{<div class="breadcrumb">([\s|\S]*)</a></span>}', $this->html, $matches
    );
    if (count($matches[1]) === 0) {
      throw new Exception(null, 500);
    }
    $categoryName = iconv('gbk', 'utf-8', end(explode('>', $matches[1][0])));
    echo $categoryName;
    exit;
    $id = null;
    Db::bind('category', array('name' => $categoryName), null, $id);
    return $id;
  }

  private function parseProductList() {
    preg_match_all(
      "{<div class='p-name'><a target='_blank'"
        ." href='http://www.360buy.com/product/([0-9]+).html'>}",
      $this->html,
      $matches
    );
    foreach ($matches[1] as $merchantProductId) {
      Db::bind('product_property_value', array(
        'merchant_product_id' => $merchantProductId,
        'property_value_id' => $this->valueId
      ), array(
        'category_id' => $this->categoryId, 'version' => $GLOBALS['VERSION']
      ));
    }
  }

  private function parseNextPage() {
    preg_match(
      '{href="([0-9-]+).html" class="next"}', $this->html, $matches
    );
    if (count($matches) > 0) {
      self::execute($matches[1]);
    }
  }
}