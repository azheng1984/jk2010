<?php
class PropertyProductListProcessor {
  private $html;
  private $page;
  private $categoryId;
  private $valueId;
  private $attributeId;

  public function execute($arguments) {
    $this->page = $arguments['page'];
    $this->valueId = $arguments['value_id'];
    $this->attributeId = $arguments['attribute_id'];
    $this->categoryId = $arguments['category_id'];
    $path = 'list?att='.$this->attributeId.'&cat='
      .$this->categoryId.'&p='.$this->page;
    $result = WebClient::get('category.dangdang.com', $path);
    if (($this->html = $result['content']) === false) {
      return $result;
    }
    $this->parseProducts($result['url']);
    $this->parseNextPage();
  }

  private function parseProducts() {
    $pattern = '{<div class="name" name="__name">.*?'
      .'<a href="http://product.dangdang.com/Product.aspx?product_id=(.*?)"}';
    preg_match_all($pattern,$this->html, $matches);
    $productIds = $matches[1];
    foreach ($productIds as $id) {
      DbProduct::addProperty($id, $this->valueId);
    }
  }

  private function parseNextPage() {
    $pattern = '{<a href="http://category.dangdang.com/'
      .'list\?att=.*?&p=.*?class="nextpage"}';
    if (preg_match($pattern, $this->html, $match) === 1) {
      $nextPage = $this->page + 1;
      DbTask::add('PropretyProductList', array(
        'attribute_id' => $this->attributeId,
        'category_id' => $this->categoryId,
        'value_id' => $this->valueId,
        'page' => $nextPage
      ));
    }
  }
}