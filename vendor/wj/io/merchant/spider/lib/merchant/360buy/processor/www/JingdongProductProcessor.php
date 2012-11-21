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
  private $merchantImageDigest;
  private $index;
  private $typeId;
  private $priceX100;
  private static $userKey = null;
  private static $userKeyExpireTime = null;
  private static $agencyNoMatchedCount = 0;
  private static $hasAgencyMatched = false;

  public function __construct($index = null, $categoryId = null) {
    $this->index = $index;
    $this->categoryId = $categoryId;
  }

  public function execute($path) {
    $this->merchantProductId = $path;
    $product = Db::getRow(
      'SELECT * FROM product WHERE merchant_product_id = ?', $path
    );
    if ($product !== false
      && intval($product['version']) === $GLOBALS['VERSION']) {
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
        return;
      }
      $this->update($product);
    } catch (Exception $exception) {
      $status = $exception->getCode();
    }
    $this->bindHistory($path, $status);
  }

  private function initialize($path) {
    $result = WebClient::get('www.360buy.com', '/product/'.$path.'.html');
    $this->url = 'www.360buy.com/product/'.$path.'.html';
    $html = $result['content'];
    $this->html = $html;
    preg_match(
      '{jqzoom[\s\S]*? src="http://(.*?)"}', $html, $matches
    );
    if (count($matches) === 0) {
      $this->saveMatchErrorLog('JingdongProductListProcessor:initialize#0');
      throw new Exception(null, 500);
    }
    $this->imageSrc = $matches[1];
    $this->merchantImageDigest = $this->getImageDigest();
    preg_match(
      '{http://gate\.360buy\.com/InitCart\.aspx.*?ptype=(.*?)[^0-9]}',
      $html,
      $matches
    );
    if (count($matches) === 0) {
      $this->saveMatchErrorLog('JingdongProductListProcessor:initialize#1');
      throw new Exception(null, 500);
    }
    $this->typeId = intval($matches[1]);
    preg_match(
      '{<div class="breadcrumb">([\s|\S]*?)<!--breadcrumb end-->}',
      $html,
      $matches
    );
    if (count($matches) === 0) {
      $this->saveMatchErrorLog('JingdongProductListProcessor:initialize#2');
      throw new Exception(null, 500);
    }
    $list = explode('&nbsp;&gt;&nbsp;', $matches[1]);
    if (count($list) < 4) {
      $this->saveMatchErrorLog('JingdongProductListProcessor:initialize#3');
      throw new Exception(null, 500);
    }
    preg_match('{>(.*?)<}', $list[2], $matches);
    if (count($matches) === 0) {
      $this->saveMatchErrorLog('JingdongProductListProcessor:initialize#4');
      throw new Exception(null, 500);
    }
    $this->categoryName = iconv('gbk', 'utf-8', $matches[1]);
    Db::bind(
      'category', array('name' => $this->categoryName), null, $this->categoryId
    );
    preg_match('{<h1>(.*?)</h1>}', $html, $matches);
    if (count($matches) === 0) {
      $this->saveMatchErrorLog('JingdongProductListProcessor:initialize#5');
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
      self::$hasAgencyMatched = true;
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
    print_r($product);
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
    if (self::$userKey === null || time() > self::$userKeyExpireTime) {
      $this->initializeCart();
    }
    $cookie = 'user-key='.self::$userKey;
    WebClient::get(
      'gate.360buy.com',
      '/InitCart.aspx?pid='.$merchantProductId.'&pcount=1&ptype='.$this->typeId,
      array(),
      $cookie,
      true
    );
    $result = WebClient::get(
      'cart.360buy.com', '/cart/miniCartService.action?method=GetCart',
      array(), $cookie, true
    );
    preg_match('$"PromotionPrice":(.*?)}$', $result['content'], $matches);
    if (count($matches) === 0) {
      self::$userKey = null;
      $this->saveMatchErrorLog('JingdongProductListProcessor:getPrice');
      throw new Exception(null, 404);
    }
    $this->resetCart($merchantProductId, $cookie, $result);
    return $matches[1];
  }

  private function initializeCart() {
    $result = WebClient::get(
      'cart.360buy.com', '/cart/initGetCurrentCart.action', array(), null, true
    );
    $header = $result['header'];
    preg_match('{user-key=(.*?);}', $result['header'], $matches);
    if (count($matches) === 0) {
      $this->saveMatchErrorLog('JingdongProductListProcessor:initializeCart');
      throw new Exception(null, 500);
    }
    self::$userKey = $matches[1];
    self::$userKeyExpireTime = time() + 24 * 3600;
  }

  private function resetCart($merchantProductId, $cookie, $response) {
    try {
      preg_match('$cart-main="{(.*?)}"$', $response['header'], $matches);
      if (count($matches) === 0) {
        self::$userKey = null;
        throw new Exception(null, 404);
      }
      $result = WebClient::get(
        'cart.360buy.com',
        '/cart/miniCartService.action?method=RemoveProduct&cartId='
        .$merchantProductId,
        array(), $cookie.';cart-main="{'.$matches[1].'}"'
      );
      if ($result['content'] !== 'null({"Result":true})') {
        $this->saveMatchErrorLog('JingdongProductListProcessor:resetCart');
        throw new Exception(null, 500);
      }
    } catch (Exception $exception) {
      self::$userKey = null;
    }
  }

  private function bindImage() {
    list($domain, $path) = explode('/', $this->imageSrc, 2);
    $result = WebClient::get($domain, '/'.$path);
    $this->saveImage($result['content']);
  }

  private function saveImage($image, $isNew = true) {
    if ($isNew) {
      ImageDb::insert($this->categoryName, $this->merchantProductId, $image);
      return;
    }
    ImageDb::update($this->categoryName, $this->merchantProductId, $image);
  }

  private function getImageDigest() {
    $fileName = end(explode('/', $this->imageSrc, 2));
    if (substr($fileName, -4, 4) !== '.jpg') {
      $this->saveMatchErrorLog('JingdongProductListProcessor:getImageDigest');
      throw new Exception(null, 500);
    }
    return substr($fileName, 0, -4);
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
      'processor' => 'Product',
      'path' => $path,
    ), $replacementColumnList);
  }

  private function saveMatchErrorLog($source) {
    Db::insert('match_error_log', array(
      'source' => $source,
      'url' => $this->url,
      'document' => $this->html,
      'time' => date('Y-m-d H:i:s')
    ));
  }

  public static function finalize() {
    if (self::$hasAgencyMatched === false
      && self::$agencyNoMatchedCount > 100000) {
      Db::insert('match_error_log', array(
      'source' => 'JingdongProductProcessor:NO_AGENCY_MATCHED',
      'time' => date('Y-m-d H:i:s')
      ));
    }
    self::$agencyNoMatchedCount = 0;
    self::$hasAgencyMatched = false;
  }
}