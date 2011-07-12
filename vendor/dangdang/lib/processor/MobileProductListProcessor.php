<?php
class MobileProductListProcessor {
  private $html;
  private $page;
  private $categoryId;

  public function execute($arguments) {
    $path = '/category.php?page='.$arguments['page'].'&cid='.$arguments['cid']
      .'&show_type=text&sort_type=list_sort_saled_desc';
    $result = WebClient::get('m.dangdang.com', $path);
    if (($this->html = $result['content']) === false) {
      return $result;
    }
    $this->categoryId = $arguments['category_id'];
    $this->page = $arguments['page'];
    $this->parseProductList();
    $this->parseNextPage();
  }

  private function parseProductList() {
    preg_match_all(
      '{<span><a href="product.php\?pid=(.*?)&.*?">.*?</a></span><br/>}',
      $this->html,
      $matches
    );
    $productIds = $matches[1];
    foreach ($productIds as $id) {
      DbTask::insert('Product', array(
        'category_id' => $this->categoryId, 'id' => $id
      ));
    }
  }

  private function parseNextPage() {
    $nextPage = $this->page + 1;
    if (preg_match(
      '{<span><a href="category.php\?page='
        .$nextPage.'&amp;cid=(.*?)&.*?">下页</a></span>}',
      $this->html,
      $match
    ) === 1) {
      DbTask::insert('MobileProductList', array(
        'cid' => $match[1],
        'category_id' => $this->categoryId,
        'page' => $nextPage
      ));
    }
  }
}