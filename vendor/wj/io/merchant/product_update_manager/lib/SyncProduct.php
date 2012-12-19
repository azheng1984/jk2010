<?php
class SyncProduct {
  private static $version;
  private static $categoryId;
  private static $merchantId;
  private static $merchantName;
  private static $productAmount;

  public static function execute($categoryId, $version, $merchantName) {
    self::$merchantId = 1;//TODO
    self::$categoryId = $categoryId;
    self::$version = $version;
    self::$merchantName = $merchantName;
    DbConnection::connect($merchantName);
    self::$productAmount = Db::getColumn(
      'SELECT product_amount FROM category WHERE id = ?',
      self::$categoryId
    );
    $productList = Db::getAll(
      'SELECT * FROM product WHERE category_id = ? ORDER BY id LIMIT 1000',
      self::$categoryId
    );
    DbConnection::close();
    self::sync($productList);
    while (count($productList) === 1000) {
      $product = end($productList);
      DbConnection::connect($merchantName);
      $productList = Db::getAll(
        'SELECT * FROM product WHERE category_id = ? AND id > ?'
          .' ORDER BY id LIMIT 1000',
        self::$categoryId, $product['id']
      );
      DbConnection::close();
      self::sync($productList);
    }
  }

  private function sync($productList) {
    foreach ($productList as $product) {
      $shoppingProduct = Db::getRow(
        'SELECT * FROM product'
          .' WHERE merchant_id = ? AND merchant_product_id = ?',
        self::$merchantId,
        $product['merchant_product_id']
      );
      if ($product['version'] < self::$version) {
        if ($shoppingProduct === false) {
          continue;
        }
        CommandSyncFile::deleteProduct($shoppingProduct['id']);
        SyncImage::delete($shoppingProduct['image_path']);
        Db::delete('product', 'id = ?', $shoppingProduct['id']);
        continue;
      }
      $imagePath = null;
      if ($shoppingProduct === false) {
        $imagePath = SyncImage::allocateImageFolder();
      }
      if (isset($product['price_to_x_100']) === false) {
        $product['price_to_x_100'] = null;
      }
      //TODO process index version
      $popularityRank =
        (self::$productAmount - $product['_index']) /
          self::$productAmount;
      if (self::$version !== $product['index_version']) {
        $popularityRank = $popularityRank / self::$productAmount;
      }
      $popularityRank = intval($popularityRank * 1000000);
      if ($shoppingProduct === false) {
        $keywordTextList = self::getKeywordTextList($product['title']);
        $columnList = array(
          'merchant_id' => 1,//TODO
          'merchant_product_id' => $product['merchant_product_id'],
          'popularity_rank' => $popularityRank,
          'uri_argument_list' => $product['merchant_product_id'],//TODO
          'image_path' => $imagePath,
          'image_digest' => $product['image_digest'],
          'title' => $product['title'],
          'price_from_x_100' => $product['price_from_x_100'],
          'price_to_x_100' => $product['price_to_x_100'],
          'agency_name' => $product['agency_name'],
          'keyword_list' => $keywordTextList,
        );
        Db::insert('product', $columnList);
        $shoppingProductId = Db::getLastInsertId();
        CommandSyncFile::insertProduct($columnList, $shoppingProductId);
        SyncImage::bind(
          self::$categoryId,
          $shoppingProductId,
          $product['merchant_product_id'],
          $imagePath
        );
        continue;
      }
      $replacementColumnList = array();
      if ($shoppingProduct['uri_argument_list'] !== $product['merchant_product_id']) {
        $replacementColumnList['uri_argument_list'] = $product['merchant_product_id'];
      }
      if (intval($shoppingProduct['popularity_rank']) !== $popularityRank) {
        $replacementColumnList['popularity_rank'] = $popularityRank;
      }
      if ($shoppingProduct['image_digest'] !== $product['image_digest']) {
        $replacementColumnList['image_digest'] = $product['image_digest'];
        SyncImage::bind(
          self::$categoryId,
          $shoppingProduct['id'],
          $product['merchant_product_id'],
          $shoppingProduct['image_path']
        );
      }
      if ($shoppingProduct['title'] !== $product['title']) {
        $replacementColumnList['title'] = $product['title'];
      }
      if ($shoppingProduct['price_from_x_100'] !== $product['price_from_x_100']) {
        $replacementColumnList['price_from_x_100'] = $product['price_from_x_100'];
      }
      if ($shoppingProduct['price_to_x_100'] !== $product['price_to_x_100']) {
        $replacementColumnList['price_to_x_100'] = $product['price_to_x_100'];
      }
      if ($shoppingProduct['agency_name'] !== $product['agency_name']) {
        $replacementColumnList['agency_name'] = $product['agency_name'];
      }
      //TODO 如果分词算法/字典更新，所有 keywords 都会更新
      if (isset($replacementColumnList['title'])) {
        $keywordTextList = self::getKeywordTextList($product['title']);
        $keywordListByKey = array();
        foreach (explode(' ', $keywordTextList) as $keyword) {
          $keywordListByKey[$keyword] = true;
        }
        $currentKeywordList = explode(' ', $keywordTextList);
        $isUpdated = false;
        foreach ($currentKeywordList as $item) {
          if (isset($keywordListByKey[$item])) {
            unset($keywordListByKey[$item]);
            continue;
          }
          $isUpdated = true;
          break;
        }
        if ($isUpdated === false && count($keywordListByKey) !== 0) {
          $isUpdated = true;
        }
        if ($isUpdated) {
          $replacementColumnList['keyword_list'] = $keywordTextList;
        }
      }
      if (count($replacementColumnList) > 0) {
        Db::update(
          'product', $replacementColumnList, 'id = ?', $shoppingProduct['id']
        );
        CommandSyncFile::updateProduct(
          $shoppingProduct['id'], $replacementColumnList
        );
      }
    }
  }

   private static function getKeywordTextList($title) {
    $keywords = SegmentationService::execute(str_replace("\n", ' ', $title));
    $list = explode(' ', $keywords);
    return implode(' ', array_unique($list));
  }
}