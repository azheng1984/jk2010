<?php
class JingdongCategoryListProcessor {
  private $categoryId;
  private $categoryVersion;

  public function execute() {
    Db::getRow(
      'SELECT * FROM catagory3 WHERE name = 2'
    );
    $result = WebClient::get('www.360buy.com', '/allSort.aspx');
    preg_match_all(
      '{products/([0-9]+)-([0-9]+)-([0-9]+).html.>(.*?)<}',
      $result['content'],
      $matches
    );
    if (count($matches[0]) === 0) {
      throw new Exception(null, 500);
    }
    $categoryBuilder = new JingDongCategoryBuilder;
    foreach ($matches[1] as $index => $levelOneCategoryId) {
      if ($matches[3][$index] === '000') {//leaf category only
        continue;
      }
      if ($levelOneCategoryId === '1713') {//publication
        continue;
      }
      if ($levelOneCategoryId === '5025') {
        //WatchProductList（brand as category）
        continue;
      }
      $categoryName = $matches[4][$index];
      $this->setCategory(iconv('gbk', 'utf-8', $categoryName));
      $path = $levelOneCategoryId.'-'
        .$matches[2][$index].'-'.$matches[3][$index];
      if ($this->categoryVersion !== $GLOBALS['VERSION']) {
        $productListProcessor = new JingdongProductListProcessor;
        $productListProcessor->execute($path);
        $categoryBuilder->execute($this->categoryId, $categoryName);
      }
    }
  }

  private function setCategory($name) {
    $category = Db::getRow(
      'SELECT * FROM catagory WHERE name = ?', $name
    );
    if ($category === false) {
      Db::insert('category', array('name' => $name));
      $this->categoryId = Db::getLastInsertId();
      $this->categoryVersion = 0;
      return;
    }
    $this->categoryId = $category['id'];
    $this->categoryVersion = $category['version'];
  }
}