<?php
class ProductProcessor {
  private $tablePrefix;
  private $html;
  private $merchantProductId;
  private $title = null;
  private $description = null;
  private $saleIndex;

  public function execute($arguments) {
    $result = WebClient::get(
      'www.360buy.com', '/product/'.$arguments['id'].'.html'
    );
    if ($result['content'] === false) {
      return $result;
    }
    $this->html = $result['content'];
    $this->saleIndex = $arguments['sale_index'];
    $this->tablePrefix = $arguments['table_prefix'];
    $this->merchantProductId = $arguments['id'];
    $this->categoryId = $arguments['category_id'];
    $this->parseTitle();
    $this->parseDescription();
    $this->parseProperties();
    $info = DbProduct::getContentMd5AndSaleIndex(
      $this->tablePrefix, $this->merchantProductId
    );
    $this->save($info);
    $matches = array();
    preg_match(
      '{jqzoom.*? src="http://(.*?)/(\S+)"}', $result['content'], $matches
    );
    if (count($matches) !== 3) {
      return $result;
    }
    DbTask::insert('Image', array(
      'id' => $arguments['id'],
      'category_id' => $arguments['category_id'],
      'domain' => $matches[1],
      'path' => $matches[2],
      'table_prefix' => $arguments['table_prefix']
    ));
    DbTask::insert('Price', array(
      'id' => $arguments['id'], 'table_prefix' => $arguments['table_prefix'])
    );
  }

  private function save($info) {
    $md5 = $this->getContentMd5();
    if ($info === false) {
      return $this->insertContent($md5);
    }
    $this->updateContent($info, $md5);
    $this->updateSaleIndex($info);
    return $info['id'];
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
          $keyId = DbProperty::getOrNewKeyId($this->tablePrefix, $key);
          $valueId = DbProperty::getOrNewValueId(
            $this->tablePrefix, $keyId, $value
          );
          DbProductProperty::replace(
            $this->tablePrefix, $this->merchantProductId, $valueId
          );
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

  private function insertContent($md5) {
    $id = DbProduct::insert(
      $this->tablePrefix, $this->merchantProductId,$this->categoryId,
      $this->title, $this->description, $md5, $this->saleIndex
    );
    DbProductUpdate::insert($this->tablePrefix, $id, 'NEW');
    return $id;
  }

  private function updateContent($md5, $info) {
    if ($info['content_md5'] === $md5) {
      DbProduct::updateContent(
        $this->tablePrefix,  $info['id'], $this->categoryId, $this->title,
        $this->description, $md5
      );
      DbProductUpdate::insert($this->tablePrefix,  $info['id'], 'CONTENT');
      return;
    }
    DbProduct::updateFlag($this->tablePrefix, $info['id']);
  }

  private function updateSaleIndex($info) {
    $previousIndex = $info  === false ? false : $info['sale_index'];
    if ($previousIndex !== $this->saleIndex) {
      DbProduct::updateSaleIndex($this->tablePrefix, $info['id'], $this->saleIndex);
      DbProductUpdate::insert($this->tablePrefix, $info['id'], 'SALE_INDEX');
    }
  }

  private function getContentMd5() {
    $propertyList = var_export(DbProductProperty::getListByMerchantProductId(
      $this->tablePrefix, $this->merchantProductId), true);
    return md5($propertyList.$this->categoryId.$this->title.$this->description);
  }
}