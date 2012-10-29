<?php
class JingdongCategoryListProcessor {
  public function execute() {
    $result = WebClient::get('www.360buy.com', '/allSort.aspx');
    preg_match_all(
      '{products/([0-9]+)-([0-9]+)-([0-9]+).html}', $result['content'], $matches
    );
    if (count($matches[1]) === 0) {
      throw new Exception(null, 500);
    }
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
      $productListProcessor = new JingdongProductListProcessor;
      $productListProcessor->execute(
        $levelOneCategoryId.'-'.$matches[2][$index].'-'.$matches[3][$index]
      );
      //TODO:触发 category 异步同步
    }
  }
}