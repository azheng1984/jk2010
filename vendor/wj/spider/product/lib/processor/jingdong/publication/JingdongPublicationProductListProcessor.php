<?php
class JingdongPublicationProductListProcessor {
  private $html;
  private $page;
  private $categoryId;

  public function execute($arguments) {
    $result = WebClient::get(
      'www.360buy.com', '/products/'.$arguments['path'].'.html'
    );
    $this->page = $arguments['page'];
    $this->html = $result['content'];
    if ($this->html === false) {
      return $result;
    }
    $this->categoryId = $this->getCategoryId($arguments);
    $this->parseProductList();
    $this->parseNextPage();
  }

  private function getCategoryId($arguments) {
    if (isset($arguments['category_id'])) {
      return $arguments['category_id'];
    }
    return Db::bind('category', array(
      'name' => $arguments['name'],
      'parent_id' => $arguments['parent_category_id']
    ));
  }

  private function parseProductList() {
    preg_match_all(
    "{<dt class=\"p-name\">\\s*<a target='_blank'"
      .' href="http://(.*?).360buy.com/(.*?).html">}',
      $this->html,
      $matches
    );
    $productIds = $matches[2];
    foreach ($productIds as $id) {
      Db::insert('task', array(
        'type' => 'PublicationProduct',
        'argument_list' => var_export(array(
          'domain' => $matches[1][0],
          'category_id' => $this->categoryId,
          'id' => $id
        ), true)
      ));
    }
  }

  private function parseNextPage() {
    preg_match(
      '{class="current".*?href="([0-9-]+).html"}',
      $this->html,
      $matches
    );
    if (count($matches) > 0) {
      $page = $this->page + 1;
      Db::insert('task', array(
        'type' => 'PublicationProductList',
        'argument_list' => var_export(array(
          'path' => $matches[1],
          'category_id' => $this->categoryId,
          'page' => $page
        ), true)
      ));
    }
  }
}