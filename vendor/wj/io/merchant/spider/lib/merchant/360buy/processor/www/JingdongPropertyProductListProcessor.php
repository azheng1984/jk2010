<?php
class JingdongPropertyProductListProcessor {
  private $html;
  private $url;
  private $valueId;
  private $categoryId;
  private static $nextPageNoMatchedCount = 0;
  private static $nextPageMatchedCount = 0;

  public function __construct($categoryId = null, $valueId = null) {
    $this->categoryId = $categoryId;
    $this->valueId = $valueId;
  }

  public function execute($path) {
    $status = 200;
    try {
      $result = WebClient::get('www.360buy.com', '/products/'.$path.'.html');
      $this->url = 'www.360buy.com/products/'.$path.'.html';
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
      '_status' => $status,
      'version' => $GLOBALS['VERSION']
    );
    if ($this->categoryId !== null) {
      $replacementColumnList['category_id'] = $this->categoryId;
    }
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
      $this->saveMatchErrorLog(
        'JingdongPropertyProductListProcessor:getValueId'
      );
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
      $this->saveMatchErrorLog(
        'JingdongPropertyProductListProcessor:getCategoryId'
      );
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
    if (count($matches[0]) === 0) {
      $this->checkProductList();
    }
    foreach ($matches[1] as $merchantProductId) {
      Db::bind('product_property_value', array(
        'merchant_product_id' => $merchantProductId,
        'property_value_id' => $this->valueId
      ), array(
        'category_id' => $this->categoryId, 'version' => $GLOBALS['VERSION']
      ));
    }
  }

  private function checkProductList() {
    preg_match('{没有找到符合条件的商品}', $this->html, $matches);
    if (count($matches) > 0) {
      return;
    }
    $this->saveMatchErrorLog(
      'JingdongPropertyProductListProcessor:checkProductList'
    );
  }

  private function parseNextPage() {
    preg_match(
      '{href="([0-9-]+).html" class="next"}', $this->html, $matches
    );
    if (count($matches) > 0) {
      ++self::$nextPageMatchedCount;
      self::execute($matches[1]);
      return;
    }
    ++self::$nextPageNoMatchedCount;
  }

  private function saveMatchErrorLog($source) {
    Db::insert('match_error_log', array(
      'source' => $source,
      'url' => $this->url,
      'document' => gzcompress($this->html),
      'time' => date('Y-m-d H:i:s')
    ));
  }
  
  public static function finalize() {
    Db::insert('match_log', array(
      'source' => 'JingdongPropertyProductListProcessor:next_page',
      'match_count' => self::$nextPageMatchedCount,
      'no_match_count' => self::$nextPageNoMatchedCount,
      'time' => date('Y-m-d H:i:s')
    ));
    self::$hasNextPageMatched = 0;
    self::$nextPageNoMatchedCount = 0;
  }
}