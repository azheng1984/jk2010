<?php
class JingdongProductProcessor {
  private $title;
  private $imageSrc;
  private $index;
  private $categoryId;
  private $merchantProductId;

  public function __construct(
    $categoryId = null, $title = null, $imageSrc = null, $index = null
  ) {
    $this->title = $title;
    $this->imageSrc = $imageSrc;
    $this->index = $index;
    $this->categoryId = $categoryId;
  }

  public function execute($path) {
    $this->merchantProductId = $path;
    $product = Db::getRow(
      'SELECT * FROM product WHERE merchant_product_id = ?', $path
    );
    if ($product === false) {
      $imageDigest = $this->bindImage();
      $price = $this->getPrice($path);
      Db::insert('product', array(
        'merchant_product_id' => $path,
        'title' => $this->title,
        'image_digest' => $imageDigest,
        'price_from_x_100' => $price * 100,
        'version' => SPIDER_VERSION
      ));
      return;
    }
  }

  private function getPrice($merchantProductId) {
    $result = WebClient::get(
      'jd2008.360buy.com',
      '/purchase/minicartservice.aspx?method=GetCart',
      array(),
      'yCartOrderLogic={&TheSkus&:[{&Id&:'.$merchantProductId.'$&Num&:1}]};'
    );
    preg_match('{"PromotionPrice":(.*?),}', $result['content'], $matches);
    if (count($matches[1]) === 0) {
      return;
    }
    return $matches[1][0] * 100;
  }

  private function bindImage($localDigest = null) {
    $remoteDigest = $this->getImageDigest();
    if ($localDigest !== $remoteDigest) {
      list($domain, $path) = explode('/', $this->imageSrc, 2);
      $result = WebClient::get($domain, $path);
      $this->saveImage($result['content']);
      return $remoteDigest;
    }
  }

  private function saveImage($image, $isNew) {
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
}