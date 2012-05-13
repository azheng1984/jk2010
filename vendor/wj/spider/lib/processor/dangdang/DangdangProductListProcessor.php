<?php
class DangdangProductListProcessor {
  private $html;
  private $categoryId;

  public function execute($arguments) {
    $this->categoryId = $arguments['category_id'];
    $this->page = $arguments['page'];
    $result = WebClient::get('category.dangdang.com', $arguments['path']);
    if (($this->html = $result['content']) === false) {
      return $result;
    }
    if ($this->page === 1) {
      $this->parsePropertyList();
    }
    $this->parseProductList();
    $this->parseNextPage();
  }

  private function parsePropertyList() {
    $pattern = '{<div class="condition_panel" id="condition_panel">'
      .'[\s|\S]*<div class="search_goods_list">}';
    if (preg_match($pattern, $this->html, $match) === 1) {
      $section = iconv('gbk', 'utf-8', $match[0]);
      foreach (explode('<div class="tip">', $section) as $item) {
        if (preg_match(
          '{<div class="conditions panel_fold">.*?}', $item, $match
        ) === 1) {
          if (preg_match(
            '{\s+<span>(.*?)：</span>}', $item, $match
          ) === 1) {
            $key = $match[1];
            if ($key === '价格') {
              continue;
            }
            $keyId = DbProperty::getOrNewKeyId($this->categoryId, $key);
            $pattern = '{<a +href="http://category.dangdang.com'
              .'(/list\?att=.*?&cat=.*?)" title="(.*?)">}';
            preg_match_all($pattern, $item, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
              $value = $match[2];
              if ($value === '其它') {
                continue;
              }
              $valueId = DbProperty::getOrNewValueId($keyId, $value);
              DbTask::insert('PropertyProductList', array(
                'path' => $match[1],
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
    $pattern = '{<div class="name" name="__name">.*?'
      .'<a href="http://product.dangdang.com/Product.aspx\?product_id=(.*?)"}';
    preg_match_all($pattern, $this->html, $matches);
    $productIds = $matches[1];
    foreach ($productIds as $id) {
      DbTask::insert('Product', array(
        'category_id' => $this->categoryId, 'id' => $id
      ));
    }
  }

  private function parseNextPage() {
    $pattern = '{<a href="http://category.dangdang.com'
      .'(/list\?cat=.*?)&p=.*?class="nextpage"}';
    if (preg_match($pattern, $this->html, $match) === 1) {
      $nextPage = $this->page + 1;
      DbTask::insert('ProductList', array(
        'path' => $match[1].'&p='.$nextPage,
        'category_id' => $this->categoryId,
        'page' => $nextPage
      ));
    }
  }
}