<?php
class JingdongProductProcessor {
  private $tablePrefix;
  private $merchantProductId;
  private $categoryId;
  private $productId;
  private $saleRank;
  private $html;
  private $imageMd5 = null;
  private $imageLastModified = null;
  private $priceX100 = null;
  private $listPriceX100 = null;

  public function execute(
    $tablePrefix, $categoryId, $merchantProductId, $saleIndex
  ) {
    $result = WebClient::get(
      'www.360buy.com', '/product/'.$merchantProductId.'.html'
    );
    $this->tablePrefix = $tablePrefix;
    $this->merchantProductId = $merchantProductId;
    $this->saleRank = 1000000000 - $saleIndex;
    $this->categoryId = $categoryId;
    $this->html = $result['content'];
    $this->productId = $this->save();
    $this->insertImageTask();
    $this->insertPriceTask();
  }

  private function save() {
    $product = Db::getRow(
      'SELECT * FROM `'.$this->tablePrefix
        .'-product` WHERE merchant_product_id = ?',
      $this->merchantProductId
    );
    $title = $this->getTitle();
    if ($product === false) {
      $sql = 'INSERT INTO `'.$this->tablePrefix.'-product`'
        .'(merchant_product_id, category_id, title, sale_rank, index_time)'
        .' VALUES(?, ?, ?, ?, NOW())';
      Db::execute($sql, $this->merchantProductId, $this->categoryId,
        $title, $this->saleRank);
      $productId = Db::getLastInsertId();
      Db::insert('`'.$this->tablePrefix.'-log`', array(
        'type' => 'NEW',
        'product_id' => $productId,
        'category_id' => $this->categoryId
      ));
      return $productId;
    }
    $columnList = array('is_updated' => 1);
    if ($product['category_id'] !== $this->categoryId) {
      $columnList['category_id'] = $this->categoryId;
      Db::insert('`'.$this->tablePrefix.'-log`', array(
        'type' => 'CATEGORY',
        'product_id' => $productId,
        'category_id' => $this->categoryId
      ));
    }
    if ($product['title'] !== $title) {
      $columnList['title'] = $title;
      Db::insert('`'.$this->tablePrefix.'-log`', array(
        'type' => 'TITLE',
        'product_id' => $productId,
        'category_id' => $this->categoryId
      ));
    }
    if ((int)$product['sale_rank'] !== $this->saleRank) {
      $columnList['sale_rank'] = $this->saleRank;
      Db::insert('`'.$this->tablePrefix.'-log`', array(
        'type' => 'SALE_RANK',
        'product_id' => $productId,
        'category_id' => $this->categoryId
      ));
    }
    Db::update(
      '`'.$this->tablePrefix.'-product`',
      $columnList,
      'id = ?',
      $product['id']
    );
    $this->listPriceX100 = $product['list_price_x_100'];
    $this->priceX100 = $product['price_x_100'];
    $this->imageLastModified = $product['image_last_modified'];
    $this->imageMd5 = $product['image_md5'];
    return $product['id'];
  }

  private function getTitle() {
    preg_match('{<h1>(.*?)<font}', $this->html, $matches);
    if (count($matches) !== 2) {
      throw new Exception;
    }
    return trim(iconv('GBK', 'utf-8', $matches[1]));
  }

  private function insertImageTask() {
    preg_match(
      '{jqzoom.*? src="http://(.*?)/(\S+)"}', $this->html, $matches
    );
    if (count($matches) !== 3) {
      throw new Exception;
    }
    $domain = $matches[1];
    $path = $matches[2];
    Db::insert('task', array('processor' => 'Image',
      'argument_list' => var_export(array(
        $this->tablePrefix,
        $this->categoryId,
        $this->productId,
        $this->imageLastModified,
        $this->imageMd5,
        $domain,
        $path,
      ), true)
    ));
  }

  private function insertPriceTask() {
    Db::insert('task', array('processor' => 'JingdongPrice',
      'argument_list' => var_export(array(
        $this->tablePrefix,
        $this->categoryId,
        $this->productId,
        $this->merchantProductId,
        $this->priceX100,
        $this->listPriceX100
      ), true)
    ));
  }
}