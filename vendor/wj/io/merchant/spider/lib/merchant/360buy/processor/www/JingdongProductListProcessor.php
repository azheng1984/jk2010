<?php
class JingdongProductListProcessor {
  private $html;
  private $url;
  private $categoryId;
  private $page;
  private $isFirstMatch;
  private static $nextPageNoMatchedCount = 0;
  private static $nextPageMatchedCount = 0;

  public function __construct($categoryId = null) {
    $this->categoryId = $categoryId;
  }

  public function execute($path, $history = null) {
    $this->isFirstMatch = true;
    $status = 200;
    try {
      $result = JingdongWebClient::get('www.360buy.com', '/products/'.$path.'.html');
      $this->html = $result['content'];
      $this->url = 'www.360buy.com/products/'.$path.'.html';
      if ($this->categoryId === null) {
        $this->categoryId = $this->getCategoryId();
        $this->isFirstMatch = false;
      }
      if ($this->page === null) {
        $this->page = $this->getPage($path);
      }
      $this->parseProductList();
      $this->parseNextPage();
    } catch (Exception $exception) {
      DbConnection::closeAll();
      if ($exception->getMessage() !== '') {
        if ($this->isFirstMatch
          && JingdongMatchChecker::execute(
            'ProductList', $path, $this->html
          ) !== false) {
          return;
        }
        $this->saveMatchErrorLog($exception->getMessage());
      }
      $status = $exception->getCode();
    }
    History::bind(
      'ProductList', $path, $status, $this->categoryId, $history
    );
  }

  private function getCategoryId() {
    preg_match(
      '{<div class="breadcrumb">\s+([\S ]*?)</a></span>}', $this->html, $matches
    );
    if (count($matches) === 0) {
      throw new Exception(
        'JingdongProductListProcessor:parseProductList#1', 500
      );
    }
    $categoryName = iconv('gbk', 'utf-8', end(explode('html">', $matches[1])));
    if (trim($categoryName) === '') {
      var_dump($categoryName);
      var_dump($this->url);
      file_put_contents(
        '/home/azheng/x.match.html',
        iconv('gbk', 'utf-8', var_export($matches, true))
      );
      file_put_contents('/home/azheng/x.html', $this->html);
      exit;
    }
    //TODO 验证
    $tmp = explode('.', end(explode('products/', $matches[1])), 2);
    $merchantCategoryId = $tmp[0];
    $id = null;
    Db::bind(
      'category',
      array('merchant_category_id' => $merchantCategoryId),
      array('name' => $categoryName),
      $id
    );
    ImageDb::tryCreateTable($id);
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
      throw new Exception(
        'JingdongProductListProcessor:parseProductList#0', 500
      );
    }
    $this->isFirstMatch = false;
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
      $this->execute($matches[1]);
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
      'source' => 'JingdongProductListProcessor:next_page',
      'match_count' => self::$nextPageMatchedCount,
      'no_match_count' => self::$nextPageNoMatchedCount,
      'time' => date('Y-m-d H:i:s'),
      'version' => $GLOBALS['VERSION']
    ));
    self::$nextPageMatchedCount = 0;
    self::$nextPageNoMatchedCount = 0;
  }
}