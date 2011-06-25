<?php
class ProductListParser {
  public function execute($client, $category, $id) {
    $result = $client->get('www.360buy.com', '/products/'.$id.'.html');
    $html = $result['content'];
    $matches = array();
    preg_match(
      '{<div id="select" [\s|\S]*<!--select end -->}', $html, $matches
    );
    if (count($matches) > 0) {
      $section = iconv('gbk', 'utf-8', $matches[0]);
      preg_match_all(
        '{<dl.*?</dl>}', $section, $matches
      );
      foreach ($matches[0] as $item) {
        preg_match_all(
          "{<dt>(.*?)</dt>}", $item, $matches
        );
        print_r($matches);
        preg_match_all(
          "{<a.*?href='(.*?)'.*?>(.*?)</a>}", $item, $matches
        );
        print_r($matches);
      }
    }
    preg_match('{<a href="http://www\.360buy\.com(/plistSearch\.aspx.*?)">}', $html, $matches);
    if (count($matches) > 0) {
      print_r($matches);
    }
    exit;
    $matches = array();
    preg_match_all(
      '{&gt;&nbsp;<a .*?www.360buy.com.*?">(.*?)</a>}', $html, $matches
    );
    $amount = count($matches[1]);
    for ($index = 1; $index < $amount; ++$index) {
      $categoryName = iconv('gbk', 'utf-8', $matches[1][$index]);
      $categoryId = $category->getOrNewId($categoryName, $categoryId);
    }
    $productList = new ProductList;
    $productList->insert($categoryId, null, 1, $html);
    preg_match_all(
      "{<div class='p-img'><a target='_blank'"
        ." href='http://www.360buy.com/product/([0-9]+).html'>}",
      $html,
      $matches
    );
    $productIds = $matches[1];
    preg_match(
      '{<a href="([0-9-]+).html" class="next">}',
      $html,
      $matches
    );
    return $productIds;
  }
}