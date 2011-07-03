<?php
class ProductGridProcessor {
  private $html;
  private $name;
  private $page;
  private $categoryId;

  public function execute($arguments) {
    $result = WebClient::get(
      'category.dangdang.com', 'list?cat='.$arguments['category_id']
    );
    $this->page = $arguments['page'];
      if (($this->html = $result['content']) === false) {
      return $result;
    }
    $this->saveContent($arguments);
    if ($this->page === 1) {
      $this->parsePropertyList();
    }
    $this->parseProductList();
    $this->parseNextPage();
  }

  private function saveContent($arguments, $url) {
    DbProductList::insert(
      $this->categoryId, null, $url, $this->page, $this->html
    );
  }

  private function parsePropertyList() {
    $pattern = '{<div class="condition_panel" id="condition_panel">'
      .'[\s|\S]*<div class="bottom_panel">}';
    if (preg_match($pattern, $this->html, $match) === 1) {
      $section = iconv('gbk', 'utf-8', $match[0]);
      foreach (explode('<div class="tip">', $section) as $item) {
        if (preg_match(
          '{<div class="conditions panel_fold">.*?}', $item, $match
        ) === 1) {
          if (preg_match(
            '{\s+<span>(.*?)</span>}', $item, $match
          ) === 1) {
            $key = $match[1];
            if ($key === '价格') {
              continue;
            }
            $keyId = DbProperty::getOrNewKeyId($this->categoryId, $key);
            $pattern = '{<a href="http://category.dangdang.com/list?att=(.*?)&cat='.$this->categoryId.'" title="(.*?)">}';
            preg_match_all($pattern, $item, $matches, PREG_SET_ORDER);
            foreach ($matches as $attribute) {
              $value = $attribute[2];
              if ($value === '全部' || $value === '其它') {
                continue;
              }
              $valueId = DbProperty::getOrNewValueId($keyId, $value);
              DbTask::add('PropertyProductList', array(
                'attribute_id' => $attribute[1],
                'category_id' => $this->categoryId,
                'value_id' => $valueId,
                'page' => 1
              ));
            }
          }
        }
      }
    }
  }

  private function parseProductList() {
    $matches = array();
    preg_match_all(
      "{<div class='p-name'><a target='_blank'"
        ." href='http://www.360buy.com/product/([0-9]+).html'>}",
      $this->html,
      $matches
    );
    $productIds = $matches[1];
    foreach ($productIds as $id) {
      DbTask::add('Product', array(
        'category_id' => $this->categoryId, 'id' => $id
      ));
    }
  }

  private function parseNextPage() {
    $matches = array();
    preg_match(
      '{class="current".*?href="([0-9-]+).html"}',
      $this->html,
      $matches
    );
    if (count($matches) > 0) {
      $page = $this->page + 1;
      DbTask::add('ProductList', array(
        'path' => $matches[1],
        'category_id' => $this->categoryId,
        'name' => $this->name,
        'page' => $page
      ));
    }
  }
}