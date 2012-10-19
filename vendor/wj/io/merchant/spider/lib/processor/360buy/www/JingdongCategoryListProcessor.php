<?php
class JingdongCategoryListProcessor {
  public function execute() {
    try {
      $result = WebClient::get('www.360buy.com', '/allSort.aspx');
    } catch (Exception $exception) {
      return;
    }
    preg_match_all(
      '{products/([0-9]+)-([0-9]+)-([0-9]+).html}',
      iconv('gbk', 'utf-8', $result['content']),
      $matches
    );
    foreach ($matches[1] as $index => $levelOneCategoryId) {
      if ($levelOneCategoryId === '1713') {//publication
        continue;
      }
      $levelTwoCategoryId = $matches[2][$index];
      $levelThreeCategoryId = $matches[3][$index];
      if ($levelOneCategoryId === '5025') {
        //WatchProductList（brand as category）
        continue;
      }
      $productListProcessor = new JingdongProductListProcessor;
      $productListProcessor->execute(
        $levelOneCategoryId.'-'.$levelTwoCategoryId
          .'-'.$levelThreeCategoryId.'.html'
      );
    }
  }
}