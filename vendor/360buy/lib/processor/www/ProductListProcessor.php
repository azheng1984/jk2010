<?php
class ProductListProcessor {
  private $html;
  private $name;
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
    $this->name = $arguments['name'];
    $this->task = new Task;
    $this->parseBreadcrumb($arguments);
    $this->saveContent();
    if ($this->page === 1) {
      $this->parsePropertyList();
      $this->parseAdvencedProperty();
    }
    $this->parseProductList();
    $this->parseNextPage();
    exit;
  }

  private function saveContent() {
    $productList = new ProductList;
    $productList->insert($this->categoryId, null, 1, $this->html);
  }

  private function parsePropertyList() {
    $matches = array();
    preg_match(
      '{<div id="select" [\s|\S]*<!--select end -->}', $this->html, $matches
    );
    if (count($matches) > 0) {
      $section = iconv('gbk', 'utf-8', $matches[0]);
      preg_match_all(
        '{<dl.*?</dl>}', $section, $matches
      );
      foreach ($matches[0] as $item) {
        preg_match_all(
          "{<dt>(.*?)：</dt>}", $item, $matches
        );
        $key = $matches[1][0];
        if ($key === '价格') {
          continue;
        }
        preg_match_all(
          "{<a.*?href='(.*?).html'.*?>(.*?)</a>}", $item, $matches
        );
        $valueLinkList = $matches[1];
        $valueList = $matches[2];
        $valueAmount = count($valueList);
        $property = new Property;
        $keyId = $property->getOrNewKeyId($this->categoryId, $key);
        for ($index = 0; $index < $valueAmount; ++$index) {
          $value = $valueList[$index];
          if ($value === '全部' || $value === '其它') {
            continue;
          }
          $valueId = $property->getOrNewValueId($keyId, $valueList[$index]);
          $this->task->add('PropertyProductList', array(
            'path' => $valueLinkList[$index],
            'category_id' => $this->categoryId,
            'value_id' => $valueId,
            'page' => 1
          ));
        }
      }
    }
  }

  private function parseBreadcrumb($arguments) {
    if (isset($arguments['category_id'])) {
      $this->categoryId = $arguments['category_id'];
      return;
    }
    $matches = array();
    preg_match_all(
      '{&gt;&nbsp;<a .*?www.360buy.com.*?">(.*?)</a>}', $this->html, $matches
    );
    $category = new Category;
    $this->categoryId = $arguments['root_category_id'];
    $amount = count($matches[1]);
    for ($index = 1; $index < $amount; ++$index) {
      $categoryName = iconv('gbk', 'utf-8', $matches[1][$index]);
      $this->categoryId = $category->getOrNewId(
        $categoryName, $this->categoryId
      );
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
      $this->task->add('Product', array(
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
      $this->task->add('ProductList', array(
        'path' => $matches[1],
        'category_id' => $this->categoryId,
        'name' => $this->name,
        'page' => $page
      ));
    }
  }
}