<?php
class JingdongProductProcessor {
  private $categoryId;
  private $title;
  private $imageSrc;
  private $index;
  private $typeId;
  private $merchantProductId;
  private static $userKey = null;
  private static $userKeyExpireTime = null;

  public function __construct(
    $categoryId = null,
    $title = null,
    $imageSrc = null,
    $typeId = null,
    $index = null
  ) {
    $this->categoryId = $categoryId;
    $this->title = $title;
    $this->imageSrc = $imageSrc;
    $this->typeId = $typeId;
    $this->index = $index;
  }

  public function execute($path) {
    $this->merchantProductId = $path;
    $product = Db::getRow(
      'SELECT * FROM product WHERE merchant_product_id = ?', $path
    );
    $status = 200;
    try {
      if ($product === false) {
        $this->insert($path);
        return;
      }
    } catch (Exception $exception) {
      echo $exception->getMessage();
      throw $exception;
      $status = $exception->getCode();
    }
    $this->bindHistory($path, $status);
    exit;
  }

  private function insert($path) {
    $price = $this->getPrice($path);
    $imageDigest = $this->bindImage();
    var_dump($price);
    $priceX100 = $price * 100;
    Db::insert('product', array(
      'merchant_product_id' => $path,
      'title' => $this->title,
      'image_digest' => $imageDigest,
      'price_from_x_100' => $priceX100,
      'version' => SPIDER_VERSION
    ));
    exit;
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
    if (substr($fileName, -4, 4) !== '.jpg') {
      throw new Exception(null, 500);
    }
    return substr($fileName, 0, -4);
  }

  private function bindHistory($path, $status) {
    $replacementColumnList = array(
      '`status`' => $status,
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