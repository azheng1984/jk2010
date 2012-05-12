<?php
class ProductProcessor {
  private $tablePrefix;
  private $html;
  private $productId;
  private $merchantProductId;
  private $uri;
  private $categoryId;
  private $saleRank;
  private $title = null;
  private $description = null;
  private $contentMd5;
  private $isContentUpdated = false;

  public function execute($arguments) {
    $this->initialize($arguments);
    $this->parseTitle();
    $this->parseDescription();
    $this->parseProperties();
    $this->buildContentMd5();
    $this->save();
    Db::update(
      $this->tablePrefix.'_product',
      array('update_flag' => 1),
      'id = ?',
      $this->productId
    );
    $this->insertImageTask();
    $this->insertPriceTask();
  }

  private function initialize($arguments) {
    $result = WebClient::get(
      'www.360buy.com', '/product/'.$arguments['id'].'.html'
    );
    if ($result['content'] === false) {
      return $result;
    }
    $this->html = $result['content'];
    $this->tablePrefix = $arguments['table_prefix'];
    $this->merchantProductId = $arguments['id'];
    $this->uri = $arguments['id'];
    $this->saleRank = 100000 - $arguments['sale_index'];
    $this->categoryId = $arguments['category_id'];
  }

  private function parseProperties() {
    preg_match(
      '{class="Ptable">(.*?)</table>}',
      $this->html,
      $matches
    );
    if (isset($matches[1])) {
      $specTable = iconv('GBK', 'utf-8', $matches[1]);
      $sections = explode('<tr><th class="tdTitle" colspan="2">', $specTable);
      foreach ($sections as $section) {
        if ($section === '') {
          continue;
        }
        preg_match(
          '{^(.*?)</th><tr>(.*)}',
          $section,
          $matches
        );
        preg_match_all(
          '{<tr><td class="tdTitle">(.*?)</td><td>(.*?)</td></tr>}',
          $matches['2'],
          $matches,
          PREG_SET_ORDER
        );
        foreach ($matches as $match) {
          $key = $match[1];
          $value = $match[2];
//           $keyId = DbProperty::getOrNewKeyId($this->tablePrefix, $key);
//           $valueId = DbProperty::getOrNewValueId(
//             $this->tablePrefix, $keyId, $value
//           );
//           DbProductProperty::replace(
//             $this->tablePrefix, $this->merchantProductId, $valueId
//           );
        }
      }
    }
  }

  private function parseDescription() {
    preg_match(
      '{<div class="content">([\s\S]*?)<!--tabcon end-->}',
      $this->html, $matches
    );
    if (count($matches) === 2) {
      $description = preg_replace('{<[\s\S]*?>}', ' ', $matches[1]);
      $description = str_replace('&nbsp;', ' ', $description);
      $description = trim(preg_replace('{\s+}', ' ', $description));
      $description = iconv('GBK', 'utf-8//IGNORE', $description);
      if ($description !== '') {
        $this->description = $description;
      }
    }
  }

  private function parseTitle() {
    preg_match(
      '{<h1>(.*?)<font}', $this->html, $matches
    );
    $this->title = trim(iconv('GBK', 'utf-8', $matches[1]));
  }

  private function buildContentMd5() {
//     $propertyList = var_export(DbProductProperty::getListByMerchantProductId(
//       $this->tablePrefix, $this->merchantProductId), true);
//     $this->contentMd5 = md5(
//       $propertyList.$this->categoryId.$this->title.$this->description
//     );
  }

  private function save() {
    $productMeta = DbProduct::getContentMd5AndSaleRankByMerchantProductId(
      $this->tablePrefix, $this->merchantProductId
    );
    if ($productMeta === false) {
      $this->insert();
      return;
    }
    $this->productId = $productMeta['id'];
    if ($productMeta['content_md5'] === $this->contentMd5
      && $this->saleRank === $productMeta['sale_rank']) {
      return;
    }
    if ($productMeta['content_md5'] === $this->contentMd5) {
      $this->updateSaleRank();
      return;
    }
    $this->update();
  }

  private function insert() {
    $this->productId = Db::insert($this->tablePrefix.'_product', array(
      'merchant_product_id' => $this->merchantProductId,
      'uri' => $this->uri,
      'category_id' => $this->categoryId,
      'title' => $this->title,
      'property_list' => $this->description,
      'content_md5' => $this->contentMd5,
      'sale_rank' => $this->saleRank
    ));
    $this->addContentUpdateLog();
  }

  private function update() {
    Db::update($this->tablePrefix.'_product', array(
      $this->productId,
      $this->categoryId,
      $this->title,
      $this->description,
      $this->contentMd5
    ));
    $this->addContentUpdateLog();
  }

  private function updateSaleRank($id) {
    DbProduct::updateSaleRank($this->tablePrefix, $id, $this->saleRank);
    DbLog::insert($this->tablePrefix, $id, 'SALE_RANK');
  }

  private function addContentUpdateLog() {
    DbLog::insert($this->tablePrefix, $this->productId, 'CONTENT');
    $this->isContentUpdated = true;
  }

  private function insertImageTask() {
    preg_match(
      '{jqzoom.*? src="http://(.*?)/(\S+)"}', $this->html, $matches
    );
    if (count($matches) !== 3) {
      throw Exception;
    }
    DbTask::insert('Image', array(
      'id' => $this->productId,
      'merchant_product_id' => $this->merchantProductId,
      'category_id' => $this->categoryId,
      'domain' => $matches[1],
      'path' => $matches[2],
      'table_prefix' => $this->tablePrefix
    ));
  }

  private function insertPriceTask() {
    DbTask::insert('Price', array(
      'id' => $this->productId,
      'merchant_product_id' => $this->merchantProductId,
      'is_content_updated' => $this->isContentUpdated,
      'table_prefix' => $this->tablePrefix
    ));
  }
}