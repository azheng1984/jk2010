<?php
class PropertyProductListProcessor {
  private $html;
  private $page;
  private $valueId;

  public function execute($arguments) {
    $this->page = $arguments['page'];
    $this->valueId = $arguments['value_id'];
    $path = $arguments['path'].'&p='.$this->page;
    $result = WebClient::get('category.dangdang.com', $path);
    if (($this->html = $result['content']) === false) {
      return $result;
    }
    $this->parseProducts();
    $this->parseNextPage();
  }

  private function parseProducts() {
    $pattern = '{<div class="name" name="__name">.*?'
      .'<a href="http://product.dangdang.com/Product.aspx\?product_id=(.*?)"}';
    preg_match_all($pattern, $this->html, $matches);
    $productIds = $matches[1];
    foreach ($productIds as $id) {
      DbProduct::addProperty($id, $this->valueId);
    }
  }

  private function parseNextPage() {
    $pattern = '{<a href="http://category.dangdang.com'
      .'(/list\?.*?cat=.*?)&p=.*?class="nextpage"}';
    if (preg_match($pattern, $this->html, $match) === 1) {
      $nextPage = $this->page + 1;
      DbTask::add('PropertyProductList', array(
        'path' => $match[1].'&p='.$nextPage,
        'value_id' => $this->valueId,
        'page' => $nextPage
      ));
    }
  }
}