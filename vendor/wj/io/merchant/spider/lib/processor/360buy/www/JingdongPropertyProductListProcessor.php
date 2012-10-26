<?php
class JingdongPropertyProductListProcessor {
  private $valueId;
  private $html;

  public function execute($path) {
    $status = 200;
    try {
      $result = WebClient::get('www.360buy.com', '/products/'.$path.'.html');
      $this->html = $result['content'];
      $this->valueId = $this->getValueId();
    } catch(Exception $exception) {
      $status = $exception->getCode();
    }
    $replacementColumnList = array(
      'status' => $status,
      'version' => SPIDER_VERSION,
    );
    Db::bind('history', array(
      'processor' => 'ProductPropertyList', 'path' => $path,
    ), $replacementColumnList);
  }

  private function getValueId() {
    if ($this->valueId !== null) {
      return $this->valueId;
    }
    $categoryId = $this->getCategoryId();
    preg_match(
      "{<dt>(.*?)</dt><dd><div class='content'>.*?class=\"curr\">(.*?)</a>}",
      $this->html, $matches
    );
    if (count($matches) !== 3) {
      throw new Exception(null, 500);
    }
    $keyName = iconv('gbk', 'utf-8', $matches[1]);
    $keyId = null;
    Db::bind(
      'property_key', array('category_id' => $categoryId, 'name' => $keyName),
      array('version', SPIDER_VERSION), $keyId
    );
    $valueName = iconv('gbk', 'utf-8', $matches[2]);
    $valueId = null;
    Db::bind(
      'property_value', array('key_id' => $keyId, 'name' => $valueName),
      array('version', SPIDER_VERSION), $valueId
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
    $merchantProductIdList = $matches[1];
    foreach ($merchantProductIdList as $merchantProductId) {
      Db::execute(
        'REPLACE INTO `product_property_value`'
          .'(merchant_product_id, property_value_id, version) VALUES(?, ?, ?)',
        $merchantProductId, $this->valueId, SPIDER_VERSION
      );
    }
  }

  private function parseNextPage() {
    preg_match(
      '{href="([0-9-]+).html.*?class="next"}', $this->html, $matches
    );
    if (count($matches) > 0) {
      self::execute($matches[1]);
    }
  }
}