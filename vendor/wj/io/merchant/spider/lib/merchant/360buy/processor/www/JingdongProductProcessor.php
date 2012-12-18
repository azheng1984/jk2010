<?php
class JingdongProductProcessor {
  private $html;
  private $url;
  private $merchantProductId;
  private $categoryId;
  private $categoryName;
  private $agencyName;
  private $title;
  private $imageSrc;
  private $imageFormat;
  private $merchantImageDigest;
  private $index;
  private $typeId;
  private $priceX100;
  private static $userKey = null;
  private static $agencyNoMatchedCount = 0;
  private static $agencyMatchedCount = 0;

  public function __construct($index = null, $categoryId = null) {
    $this->index = $index;
    $this->categoryId = $categoryId;
  }

  public function execute($path, $history = null) {
    $this->merchantProductId = $path;
    $product = Db::getRow(
      'SELECT * FROM product WHERE merchant_product_id = ?', $path
    );
    if ($product !== false
      && intval($product['version']) === $GLOBALS['VERSION']) {
      $this->updateIndex($product);
      return;
    }
    if ($product !== false && $this->categoryId === null) {
      $this->categoryId = $product['category_id'];
    }
    $status = 200;
    try {
      $this->initialize($path);
      if ($product === false) {
        $this->insert($path);
      } else {
        $this->update($product);
      }
    } catch (Exception $exception) {
      DbConnection::closeAll();
      if ($exception->getMessage() !== '') {
        if (JingdongMatchChecker::execute(
          'Product', $path, $this->html
        ) !== false) {
          return;
        }
        $this->saveMatchErrorLog($exception->getMessage());
      }
      $status = $exception->getCode();
    }
    History::bind('Product', $path, $status, $this->categoryId, $history);
  }

  private function updateIndex($product) {
    if ($this->categoryId !== $product['category_id']
     || $this->index === null || $this->index === intval($product['_index'])) {
      return;
    }
    Db::update(
      'product',
      array('_index' => $this->index, 'index_version' => $GLOBALS['VERSION']),
      'merchant_product_id = ?',
      $product['merchant_product_id']
    );
  }

  private function initialize($path) {
    $result = JingdongWebClient::get('www.360buy.com', '/product/'.$path.'.html');
    $this->url = 'www.360buy.com/product/'.$path.'.html';
    $html = $result['content'];
    $this->html = $html;
    preg_match(
      '{jqzoom[\s\S]*? src="http://(.*?)"}', $html, $matches
    );
    if (count($matches) === 0) {
      throw new Exception('JingdongProductProcessor:initialize#0', 500);
    }
    $this->imageSrc = $matches[1];
    //fix url bug: 可能会出现反斜杠，比如 id:123385
    //可能出现 http://img13.360buyimg.com/n1/null id:396603，使用空图片补充
    if (strpos($this->imageSrc, '\\') !== false) {
      $this->imageSrc = str_replace('\\', '/', $this->imageSrc);
    }
    $this->merchantImageDigest = $this->getImageDigest();
    preg_match(
      '{http://gate\.360buy\.com/InitCart\.aspx.*?ptype=(.*?)[^0-9]}',
      $html,
      $matches
    );
    if (count($matches) === 0) {
      $this->saveMatchErrorLog('JingdongProductProcessor:initialize#1');
      throw new Exception(null, 500);
    }
    $this->typeId = intval($matches[1]);
    preg_match(
      '{<div class="breadcrumb">([\s|\S]*?)<!--breadcrumb end-->}',
      $html,
      $matches
    );
    if (count($matches) === 0) {
      $this->saveMatchErrorLog('JingdongProductProcessor:initialize#2');
      throw new Exception(null, 500);
    }
    $list = explode('&nbsp;&gt;&nbsp;', $matches[1]);
    if (count($list) < 4) {
      $this->saveMatchErrorLog('JingdongProductProcessor:initialize#3');
      throw new Exception(null, 500);
    }
    preg_match('{>(.*?)<}', $list[2], $matches);
    if (count($matches) === 0) {
      $this->saveMatchErrorLog('JingdongProductProcessor:initialize#4');
      throw new Exception(null, 500);
    }
    $this->categoryName = iconv('gbk', 'utf-8', $matches[1]);
    if (trim($this->categoryName) === '') {
      error_log(var_export($this->categoryName, true));
      var_dump($this->categoryName);
      var_dump($path);
      file_put_contents('/home/azheng/x.html', $this->html);
      exit;
    }
    preg_match('{/products/(.*?)\.html}', $list[2], $matches);
    //TODO 验证
    $merchantCategoryId = $matches[1];
    Db::bind(
      'category', array('merchant_category_id' => $merchantCategoryId),
      array('name' => $this->categoryName), $this->categoryId
    );
    ImageDb::tryCreateTable($this->categoryId);
    preg_match('{<h1>(.*?)</h1>}', $html, $matches);
    if (count($matches) === 0) {
      $this->saveMatchErrorLog('JingdongProductProcessor:initialize#5');
      throw new Exception(null, 500);
    }
    $this->title = iconv('gbk', 'utf-8', $matches[1]);
    preg_match(
      '{"http://mall.360buy.com/index-[0-9]*\.html" target="_blank">(.*?)<}',
      $html,
      $matches
    );
    $hasAgency = (count($matches) !== 0);
    if ($hasAgency) {
      ++self::$agencyMatchedCount;
      $this->agencyName = iconv('gbk', 'utf-8', $matches[1]);
    }
    if ($hasAgency === false) {
      ++self::$agencyNoMatchedCount;
    }
    $this->priceX100 = $this->getPrice($path) * 100;
  }

  private function insert($path) {
    $this->bindImage();
    $product = array(
      'merchant_product_id' => $path,
      'category_id' => $this->categoryId,
      'title' => $this->title,
      'merchant_image_digest' => $this->merchantImageDigest,
      'image_digest' =>  md5($this->merchantImageDigest),
      'price_from_x_100' => $this->priceX100,
      'version' => $GLOBALS['VERSION']
    );
    if ($this->index !== null) {
      $product['_index'] = $this->index;
      $product['index_version'] = $GLOBALS['VERSION'];
    }
    if ($this->agencyName !== null) {
      $product['agency_name'] = $this->agencyName;
    }
    //print_r($product);
    Db::insert('product', $product);
  }

  private function update($product) {
    $updateColumnList = array();
    if ($product['category_id'] !== $this->categoryId) {
      $updateColumnList['category_id'] = $this->categoryId;
    }
    if ($product['agency_name'] !== $this->agencyName) {
      $updateColumnList['agency_name'] = $this->agencyName;
    }
    if ($product['title'] !== $this->title) {
      $updateColumnList['title'] = $this->title;
    }
    if ($product['merchant_image_digest'] !== $this->merchantImageDigest) {
      $this->bindImage();
      $updateColumnList['merchant_image_digest'] = $this->merchantImageDigest;
      $updateColumnList['image_digest'] = md5($this->merchantImageDigest);
    }
    if (intval($product['price_from_x_100']) !== (int)$this->priceX100) {
      $updateColumnList['price_from_x_100'] = $this->priceX100;
    }
    if ($this->index !== null && intval($product['_index']) !== $this->index) {
      $updateColumnList['_index'] = $this->index;
    }
    if ($this->index !== null
      && intval($product['index_version']) !== $GLOBALS['VERSION']) {
      $updateColumnList['index_version'] = $GLOBALS['VERSION'];
    }
    $updateColumnList['version'] = $GLOBALS['VERSION'];
    Db::update(
      'product',
      $updateColumnList,
      'merchant_product_id = ?',
      $product['merchant_product_id']
    );
  }

  private function getPrice($merchantProductId) {
    $this->initializeCart();
    $cookie = 'user-key='.self::$userKey;
    JingdongWebClient::get(
      'gate.360buy.com',
      '/InitCart.aspx?pid='.$merchantProductId.'&pcount=1&ptype='.$this->typeId,
      array(),
      $cookie,
      true
    );
    $result = JingdongWebClient::get(
      'cart.360buy.com', '/cart/miniCartService.action?method=GetCart',
      array(), $cookie, true
    );
    preg_match('$"PromotionPrice":(.*?)}$', $result['content'], $matches);
    if (count($matches) === 0) {
      self::$userKey = null;
      $this->saveMatchErrorLog(
        'JingdongProductProcessor:getPrice', $result['content']
      );
      throw new Exception(null, 404);
    }
    return $matches[1];
  }

  private function initializeCart() {
    $result = JingdongWebClient::get(
      'cart.360buy.com', '/cart/initGetCurrentCart.action', array(), null, true
    );
    $header = $result['header'];
    preg_match('{user-key=(.*?);}', $result['header'], $matches);
    if (count($matches) === 0) {
      $this->saveMatchErrorLog(
        'JingdongProductProcessor:initializeCart', var_export($result, true)
      );
      throw new Exception(null, 500);
    }
    self::$userKey = $matches[1];
  }

  private function bindImage() {
    list($domain, $path) = explode('/', $this->imageSrc, 2);
    $result = JingdongWebClient::get($domain, '/'.$path);
    $image = null;
    try {
      $thumb = new Imagick();
      $thumb->readImageBlob($result['content']);
      $thumb->resizeImage(180, 180, Imagick::FILTER_LANCZOS, 1);
      $thumb->stripImage();
      if ($this->imageFormat !== 'jpg') {
        $thumb->setImageFormat('jpeg');
      }
      $image = $thumb->getImageBlob();
      $thumb->clear();
      $thumb->destroy();
    } catch (Exception $exception) {
      //TODO log
      $thumb->clear();
      $thumb->destroy();
      throw new Exception(null, 500);
    }
    if (ImageDb::hasImage($this->categoryId, $this->merchantProductId)) {
      ImageDb::update(
        $this->categoryId, $this->merchantProductId, $image
      );
      return;
    }
    ImageDb::insert(
      $this->categoryId, $this->merchantProductId, $image
    );
  }

  private function getImageDigest() {
    $fileName = end(explode('/', $this->imageSrc, 2));
    $postfix = substr($fileName, -4, 4);
    if ($postfix === '.jpg') {
      $this->imageFormat = 'jpg';
    } elseif ($postfix === '.png') {
      $this->imageFormat = 'png';
    } else {
      $this->saveMatchErrorLog('JingdongProductProcessor:getImageDigest');
      throw new Exception(null, 500);
    }
    return substr($fileName, 0, -4);
  }

  private function saveMatchErrorLog($source, $html = null) {
    if ($html === null) {
      $html = $this->html;
    }
    Db::insert('match_error_log', array(
      'source' => $source,
      'url' => $this->url,
      'document' => gzcompress($html),
      'time' => date('Y-m-d H:i:s'),
      'version' => $GLOBALS['VERSION']
    ));
  }

  public static function finalize() {
    Db::insert('match_log', array(
      'source' => 'JingdongProductProcessor:next_page',
      'match_count' => self::$agencyMatchedCount,
      'no_match_count' => self::$agencyNoMatchedCount,
      'time' => date('Y-m-d H:i:s'),
      'version' => $GLOBALS['VERSION']
    ));
    self::$agencyMatchedCount = 0;
    self::$agencyNoMatchedCount = 0;
  }
}