<?php
class JingdongProductProcessor {
  private $categoryId;
  private $merchantId;
  private $title;
  private $imageSrc;
  private $index;
  private $typeId;
  private $merchantProductId;
  private static $userKey = null;
  private static $userKeyExpireTime = null;

  public function execute($path) {
    $this->merchantProductId = $path;
    $this->initialize($path);
    $product = Db::getRow(
      'SELECT * FROM product WHERE merchant_product_id = ?', $path
    );
    $status = 200;
    try {
      if ($product === false) {
        $this->insert($path);
        return;
      }
      $this->update($path);
    } catch (Exception $exception) {
      $status = $exception->getCode();
    }
    $this->bindHistory($path, $status);
  }

  private function initialize($path) {
    $result = WebClient::get('www.360buy.com', '/product/'.$path.'.html');
    $html = $result['content'];
    preg_match(
      '{jqzoom[\s\S]*? src="http://(.*?)"}', $html, $matches
    );
    if (count($matches) === 0) {
      throw new Exception(null, 500);
    }
    $this->imageSrc = $matches[1];
    preg_match(
      '{http://gate\.360buy\.com/InitCart\.aspx.*?ptype=(.*?)[^0-9]}',
      $html,
      $matches
    );
    if (count($matches) === 0) {
      throw new Exception(null, 500);
    }
    $this->typeId = intval($matches[1]);
    preg_match(
      '{<div class="breadcrumb">([\s|\S]*?)<!--breadcrumb end-->}', $html, $matches
    );
    if (count($matches) === 0) {
      throw new Exception(null, 500);
    }
    $list = explode('&nbsp;&gt;&nbsp;', $matches[1]);
    if (count($list) < 4) {
      throw new Exception(null, 500);
    }
    preg_match('{>(.*?)<}', $list[2], $matches);
    if (count($matches) === 0) {
      throw new Exception(null, 500);
    }
    $categoryName = iconv('gbk', 'utf-8', $matches[1]);
    Db::bind('category', array('name' => $categoryName), null, $this->categoryId);
    preg_match('{<h1>(.*?)</h1>}', $html, $matches);
    if (count($matches) === 0) {
      throw new Exception(null, 500);
    }
    $this->title = iconv('gbk', 'utf-8', $matches[1]);
    preg_match('{"http://mall.360buy.com/index-[0-9]*\.html" target="_blank">(.*?)<}', $html, $matches);
    if (count($matches) !== 0) {
      $merchantName = iconv('gbk', 'utf-8', $matches[1]);
      $this->setMerchant($merchantName);
    }
  }

  private function setMerchant($name) {
    
  }

  private function insert($path) {
    $price = $this->getPrice($path);
    $imageDigest = $this->bindImage();
    $priceX100 = $price * 100;
    Db::insert('product', array(
      'merchant_product_id' => $path,
      'category_id' => $this->categoryId,
      'title' => $this->title,
      'image_digest' => $imageDigest,
      'price_from_x_100' => $priceX100,
      'version' => SPIDER_VERSION
    ));
  }

  private function update() {
    
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
        throw new Exception(null, 500);
      }
    } catch (Exception $exception) {
      self::$userKey = null;
    }
  }

  private function bindImage($localDigest = null) {
    $remoteDigest = $this->getImageDigest();
    if ($localDigest !== $remoteDigest) {
      list($domain, $path) = explode('/', $this->imageSrc, 2);
      $result = WebClient::get($domain, '/'.$path);
      $this->saveImage($result['content']);
    }
    return $remoteDigest;
  }

  private function saveImage($image, $isNew = true) {
    if ($isNew) {
      ImageDb::insert($this->categoryId, $this->merchantProductId, $image);
      return;
    }
    ImageDb::update($this->categoryId, $this->merchantProductId, $image);
  }

  private function getImageDigest() {
    $fileName = end(explode('/', $this->imageSrc));
    if (substr($fileName, -4, 4) !== '.jpg'
      || substr($fileName, 0, 3) !== 'n1/') {
      throw new Exception(null, 500);
    }
    return substr(substr($fileName, 0, -4), 0, 3);
  }

  private function bindHistory($path, $status) {
    $replacementColumnList = array(
      '_status' => $status,
      'version' => SPIDER_VERSION,
    );
    if ($status === 200) {
      $replacementColumnList['last_ok_date'] = date('Y-m-d');
    }
    Db::bind('history', array(
      'processor' => 'Product', 'path' => $path,
    ), $replacementColumnList);
  }
}