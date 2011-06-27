<?php
class PublicationProductListProcessor {
  private $html;
  private $page;
  private $categoryId;
  private $task;

  public function execute($arguments) {
    $client = new WebClient;
    $result = $client->get(
      'www.360buy.com', '/products/'.$arguments['path'].'.html'
    );
    $this->page = $arguments['page'];
    $this->html = $result['content'];
    $this->categoryId = $this->getCategoryId($arguments);
    $this->task = new Task;
    $this->saveContent();
    $this->parseProductList();
    $this->parseNextPage();
  }

  private function saveContent() {
    $productList = new ProductList;
    $productList->insert($this->categoryId, null, $this->page, $this->html);
  }

  private function getCategoryId($arguments) {
    if (isset($arguments['category_id'])) {
      return $arguments['category_id'];
    }
    $category = new Category;
    return $category->getOrNewId(
      $arguments['name'], $arguments['parent_category_id']
    );
  }

  private function parseProductList() {
    $matches = array();
    preg_match_all(
    "{<dt class=\"p-name\">\\s*<a target='_blank'"
      .' href="http://(.*?).360buy.com/(.*?).html">}',
      $this->html,
      $matches
    );
    $productIds = $matches[2];
    foreach ($productIds as $id) {
      $this->task->add('PublicationProduct', array(
        'domain' => $matches[1][0],
        'category_id' => $this->categoryId,
        'id' => $id
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
      $this->task->add('PublicationProductList', array(
        'path' => $matches[1],
        'category_id' => $this->categoryId,
        'page' => $page
      ));
    }
  }
}