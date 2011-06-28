<?php
class PropertyProductListProcessor {
  private $html;
  private $page;
  private $categoryId;
  private $valueId;

  public function execute($arguments) {
    $result = WebClient::get(
      'www.360buy.com', '/products/'.$arguments['path'].'.html'
    );
    $this->html = $result['content'];
    $this->valueId = $arguments['value_id'];
    $this->categoryId = $arguments['category_id'];
    $this->page = $arguments['page'];
    $this->saveContent();
    $this->parseNextPage();
  }

  private function saveContent() {
    DbProductList::insert($this->categoryId, $this->valueId, $this->page, $this->html);
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
      DbTask::add('PropertyProductList', array(
        'path' => $matches[1],
        'value_id' => $this->valueId,
        'category_id' => $this->categoryId,
        'page' => $page
      ));
    }
  }
}