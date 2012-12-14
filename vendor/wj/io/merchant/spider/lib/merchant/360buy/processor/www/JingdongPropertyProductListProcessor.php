<?php
//TODO 出现无限等待
class JingdongPropertyProductListProcessor {
  private $html;
  private $url;
  private $valueId;
  private $categoryId;
  private static $nextPageNoMatchedCount = 0;
  private static $nextPageMatchedCount = 0;
  private static $cache = null;
  private $isHomePage;

  public function __construct($categoryId = null, $valueId = null) {
    $this->categoryId = $categoryId;
    $this->valueId = $valueId;
  }

  public function execute($path, $history = null) {
    $status = 200;
    try {
      $result = JingdongWebClient::get('www.360buy.com', '/products/'.$path.'.html');
      $this->url = 'www.360buy.com/products/'.$path.'.html';
      $this->html = $result['content'];
      if ($this->valueId === null) {
        $this->valueId = $this->getValueId();
      }
      $this->parseProductList();
      $this->parseNextPage();
    } catch(Exception $exception) {
      DbConnection::closeAll();
      if ($exception->getMessage() !== null) {
        if ($this->isHomePage
          && JingdongMatchChecker::execute(
            'PropertyProductList', $path, $this->html
          ) !== false) {
          return;
        }
        $this->saveMatchErrorLog($exception->getMessage());
      }
      $status = $exception->getCode();
    }
    History::bind(
      'PropertyProductList', $path, $status, $this->categoryId, $history
    );
  }

  private function getValueId() {
    preg_match(
      "{<dt>(.*?)</dt><dd><div class='content'>.*?class=\"curr\">(.*?)</a>}",
      $this->html, $matches
    );
    if (count($matches) !== 3) {
      throw new Exception(
        'JingdongPropertyProductListProcessor:getValueId', 500
      );
    }
    $this->isHomePage = false;
    $keyName = str_replace('：', '', iconv('gbk', 'utf-8', $matches[1]));
    $keyId = $this->getKeyId($keyName);
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

  private function getKeyId($keyName) {
    if ($this->categoryId === null) {
      $this->categoryId = $this->getCategoryId();
    }
    if (isset(self::$cache['key_list'][$keyName])) {
      return self::$cache['key_list'][$keyName];
    }
    $keyId = null;
    Db::bind(
      'property_key',
      array(
        'category_id' => $this->categoryId,
        'name' => $keyName
      ),
      array('version' => $GLOBALS['VERSION']),
      $keyId
    );
    self::$cache['key_list'][$keyName] = $keyId;
    return $keyId;
  }

  private function getCategoryId() {
    preg_match(
      '{<div class="breadcrumb">\s+([\S ]*?)</a></span>}', $this->html, $matches
    );
    if (count($matches) === 0) {
      $this->saveMatchErrorLog(
        'JingdongPropertyProductListProcessor:getCategoryId'
      );
      throw new Exception(null, 500);
    }
    $categoryName = iconv('gbk', 'utf-8', end(explode('>', $matches[1])));
    $this->initializeCache($categoryName);
    if (self::$cache['category']['name'] === $categoryName) {
      return self::$cache['category']['id'];
    }
    $id = null;
    Db::bind('category', array('name' => $categoryName), null, $id);
    if (trim($categoryName) === '') {
      var_dump($categoryName);
      var_dump($this->url);
      file_put_contents('/home/azheng/x.match.html', iconv('gbk', 'utf-8', var_export($matches, true)));
      file_put_contents('/home/azheng/x.html', $this->html);
      exit;
    }
    ImageDb::tryCreateTable($id);
    self::$cache['category']['name'] = $categoryName;
    self::$cache['category']['id'] = $id;
    return $id;
  }

  private function initializeCache($categoryName) {
    if (self::$cache === null
      || $GLOBALS['VERSION'] !== self::$cache['version']
      || self::$cache['category']['name'] !== $categoryName) {
      self::$cache = array(
        'version' => $GLOBALS['VERSION'],
        'category' => array('name' => null),
        'key_list' => array()
      );
    }
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
    preg_match(
      '{'.iconv('utf-8', 'gbk', '没有找到符合条件的商品').'}',
      $this->html,
      $matches
    );
    if (count($matches) > 0) {
      return;
    }
    throw new Exception(
      'JingdongPropertyProductListProcessor:checkProductList', 500
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
      'time' => date('Y-m-d H:i:s'),
      'version' => $GLOBALS['VERSION']
    ));
  }

  public static function finalize() {
    Db::insert('match_log', array(
      'source' => 'JingdongPropertyProductListProcessor:next_page',
      'match_count' => self::$nextPageMatchedCount,
      'no_match_count' => self::$nextPageNoMatchedCount,
      'time' => date('Y-m-d H:i:s'),
      'version' => $GLOBALS['VERSION']
    ));
    self::$nextPageMatchedCount = 0;
    self::$nextPageNoMatchedCount = 0;
  }
}