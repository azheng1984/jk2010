<?php
//TODO history _status 0
class JingdongProductListProcessor {
  private $html;
  private $url;
  private $categoryId;
  private $page;
  private static $nextPageNoMatchedCount = 0;
  private static $nextPageMatchedCount = 0;
  private static $propertyListNoMatchedCount = 0;
  private static $propertyListMatchedCount = 0;

  public function __construct($categoryId = null) {
    $this->categoryId = $categoryId;
  }

  public function execute($path) {
    $status = 200;
    try {
      $result = WebClient::get('www.360buy.com', '/products/'.$path.'.html');
      $this->html = $result['content'];
      
      $this->url = 'www.360buy.com/products/'.$path.'.html';
      if ($this->categoryId === null) {
        $this->categoryId = $this->getCategoryId();
      }
      if ($this->page === null) {
        $this->page = $this->getPage($path);
      }
      $this->parseProductList();
      if ($this->page === 1) {
        $this->parsePropertyList();
      }
      $this->parseNextPage();
    } catch (Exception $exception) {
      $status = $exception->getCode();
    }
    $this->bindHistory($path, $status);
  }

  private function getCategoryId() {
    preg_match(
      '{<div class="breadcrumb">([\s|\S]*?)</a></span>}', $this->html, $matches
    );
    if (count($matches) === 0) {
      $this->saveMatchErrorLog('JingdongProductListProcessor:parseProductList#1');
      throw new Exception(null, 500);
    }
    $categoryName = iconv('gbk', 'utf-8', end(explode('html">', $matches[1])));
    $id = null;
    Db::bind('category', array('name' => $categoryName), null, $id);
    return $id;
  }

  private function getPage($path) {
    $list = explode('-', $path);
    if (count($list) === 3) {
      return 1;
    }
    return intval(end($list));
  }

  private function parseProductList() {
    preg_match('{id="plist"([\s|\S]*)<!--plist end-->}', $this->html, $matches);
    if (count($matches) === 0) {
      $this->saveMatchErrorLog('JingdongProductListProcessor:parseProductList#0');
      throw new Exception(null, 500);
    }
    $list = explode('<li', $matches[1]);
    array_shift($list);
    if (count($list) < 2) {
      $this->saveMatchErrorLog('JingdongProductListProcessor:parseProductList#1');
      throw new Exception(null, 500);
    }
    $index = ($this->page - 1) * 36;
    foreach ($list as $item) {
      preg_match(
        "{<div class='p-name'><a target='_blank'"
          ." href='http://www.360buy.com/product/([0-9]+).html'>}",
        $item, $matches
      );
      if (count($matches) === 0) {
        $this->saveMatchErrorLog('JingdongProductListProcessor:parseProductList#2');
        throw new Exception(null, 500);
      }
      $merchantProductId = $matches[1];
      $processor = new JingdongProductProcessor($index, $this->categoryId);
      $processor->execute($merchantProductId);
      ++$index;
    }
  }

  private function parseNextPage() {
    preg_match(
      '{href="([0-9-]+).html" class="next"}', $this->html, $matches
    );
    if (count($matches) > 0) {
      ++self::$nextPageMatchedCount;
      ++$this->page;
      self::execute($matches[1]);
      return;
    }
    ++self::$nextPageNoMatchedCount;
  }

  private function parsePropertyList() {
    preg_match(
      '{<div id="select" [\s|\S]*<!--select end -->}', $this->html, $matches
    );
    if (count($matches) === 0) {
      ++self::$propertyListNoMatchedCount;
      return;
    }
    ++self::$propertyListMatchedCount;
    $section = iconv('gbk', 'utf-8', $matches[0]);
    preg_match_all('{<dl.*?</dl>}', $section, $matches);
    if (count($matches[0]) === 0) {
      $this->saveMatchErrorLog('JingdongProductListProcessor:parsePropertyList#0');
    }
    $keyIndex = 0;
    foreach ($matches[0] as $item) {
      preg_match_all(
        "{<dt>(.*?)：</dt>}", $item, $matches
      );
      if (count($matches[0]) === 0) {
        $this->saveMatchErrorLog('JingdongProductListProcessor:parsePropertyList#1');
      }
      $keyName = $matches[1][0];
      if ($keyName === '价格' || $keyName === '类别') {
        continue;
      }
      preg_match_all(
        "{<a.*?href='(.*?).html'.*?>(.*?)</a>}", $item, $matches
      );
      if (count($matches[0]) === 0) {
        $this->saveMatchErrorLog('JingdongProductListProcessor:parsePropertyList#2');
      }
      $valueLinkList = $matches[1];
      $valueList = $matches[2];
      $valueAmount = count($valueList);
      $keyId = null;
      Db::bind('property_key', array(
        'category_id' => $this->categoryId, 'name' => $keyName
      ), array(
        '_index' => $keyIndex, 'version' => $GLOBALS['VERSION']
      ), $keyId);
      for ($valueIndex = 0; $valueIndex < $valueAmount; ++$valueIndex) {
        $valueName = $valueList[$valueIndex];
        if ($valueName === '全部' || $valueName === '其它'
          || $valueName === '其他'.$keyName || $valueName === '不限') {
          continue;
        }
        $valueId = null;
        Db::bind('property_value', array(
          'key_id' => $keyId, 'name' => $valueList[$valueIndex]
        ), array(
          '_index' => $valueIndex, 'version' => $GLOBALS['VERSION']
        ), $valueId);
        $path = $valueLinkList[$valueIndex];
        $processor = new JingdongPropertyProductListProcessor(
          $this->categoryId, $valueId
        );
        $processor->execute($path);
      }
      ++$keyIndex;
    }
  }

  private function bindHistory($path, $status) {
    $replacementColumnList = array(
      '_status' => $status,
      'version' => $GLOBALS['VERSION'],
    );
    if ($this->categoryId !== null) {
      $replacementColumnList['category_id'] = $this->categoryId;
    }
    if ($status === 200) {
      $replacementColumnList['last_ok_date'] = date('Y-m-d');
    }
    Db::bind('history', array(
      'processor' => 'ProductList', 'path' => $path,
    ), $replacementColumnList);
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
      'source' => 'JingdongProductListProcessor:next_page',
      'match_count' => self::$nextPageMatchedCount,
      'no_match_count' => self::$nextPageNoMatchedCount,
      'time' => date('Y-m-d H:i:s')
    ));
    self::$nextPageMatchedCount = 0;
    self::$nextPageNoMatchedCount = 0;
    Db::insert('match_log', array(
      'source' => 'JingdongProductListProcessor:property_list',
      'match_count' => self::$propertyListMatchedCount,
      'no_match_count' => self::$propertyListNoMatchedCount,
      'time' => date('Y-m-d H:i:s')
    ));
    self::$propertyListMatchedCount = 0;
    self::$propertyListNoMatchedCount = 0;
  }
}