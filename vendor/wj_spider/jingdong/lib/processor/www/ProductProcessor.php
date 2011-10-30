<?php
class ProductProcessor {
  private $tablePrefix;
  private $html;
  private $merchantProductId;

  public function execute($arguments) {
    $result = WebClient::get(
      'www.360buy.com', '/product/'.$arguments['id'].'.html'
    );
    if ($result['content'] === false) {
      return $result;
    }
    $this->html = $result['content'];
    $this->tablePrefix = $arguments['table_prefix'];
    $this->merchantProductId = $arguments['id'];
    $this->categoryId = $arguments['category_id'];
    $this->parseTitle();
    $this->parseDescription();
    $this->parseProperties();
    $this->save();
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

  private function save() {
    $contentInfo = DbProduct::getContentInfo(
      $this->tablePrefix, $this->merchantProductId
    );
    $md5 = $this->getContentMd5();
    if ($contentInfo === false) {
      $this->insertContent($md5);
      return;
    }
    $this->updateContent($contentInfo['id'], $md5);
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
      $htmlTagRemoved = preg_replace('{<[\s\S]*?>}', ' ', $matches[1]);
      $htmlTagRemoved = str_replace('&nbsp;', ' ', $htmlTagRemoved);
      $spaceRemoved = trim(preg_replace('{\s+}', ' ', $htmlTagRemoved));
      $this->description = iconv('GBK', 'utf-8//IGNORE', $spaceRemoved);
    }
  }

  private function parseTitle() {
    preg_match(
      '{<h1>(.*?)<font}', $this->html, $matches
    );
    $this->title = trim(iconv('GBK', 'utf-8', $matches[1]));
  }

  private function insertContent($md5) {
    $id = DbProduct::insert($this->tablePrefix, $this->merchantProductId,
      $this->categoryId, $this->title, $this->description, $md5
    );
    DbProductUpdate::insert($this->tablePrefix, $id, 'CONTENT');
  }

  private function updateContent($id, $md5) {
    DbProduct::updateContent(
      $this->tablePrefix, $id, $this->categoryId, $this->title,
      $this->description, $md5
    );
    DbProductUpdate::insert($this->tablePrefix, $id, 'CONTENT');
  }

  private function getContentMd5() {
    $propertyList = var_export(DbProductProperty::getListByMerchantProductId(
      $this->tablePrefix, $this->merchantProductId), true);
    return md5($propertyList.$this->categoryId.$this->title.$this->description);
  }
}