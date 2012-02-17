<?php
class SearchProductListScreen {
  public static function render($searchScreen) {
    $keywordList =
      SegmentationService::execute($GLOBALS['QUERY']['name']);
    $metaList = array();
    $index = 0;
    $hasCategory = isset($GLOBALS['CATEGORY']);
    echo '<ol>';
    foreach ($GLOBALS['SEARCH_RESULT']['matches'] as $id => $result) {
      $product = DbProduct::get($id);
      $merchant = DbMerchant::get($product['merchant_id']);
      $title = self::highlight($product['title'], $keywordList);
      $description = self::highlight(
        self::excerpt($product['description']), $keywordList
      );
      echo '<li>',
        '<div class="image"><a href="" target="_blank" rel="nofollow">',
        '<img alt="', $title, '" src="http://img.dev.huobiwanjia.com/',
        $product['id'], '.jpg"/></a></div>',//image
        '<h3><a href="" target="_blank" rel="nofollow">',
        $title, '</a></h3>',//title
        '<div class="price">&yen;<span>',
        $product['lowest_price_x_100']/100, '</span></div>',//price
        '<p>', $description, '&hellip;</p>',//description
        '<div class="merchant">', $merchant['name'], '</div>',//merchant
        '</li>';
      $metaList[] = self::getMeta($product, $hasCategory);
    }
    echo '<ol>';
    self::renderMetaList($metaList);
  }

  private static function getMeta($product, $hasCategory) {
    $meta = array();
    if ($product['query_name'] !== null) {
      $meta[0] = $product['query_name'];
    }
    if ($hasCategory === false && $product['category_name'] !== null) {
      $meta[1] = $product['category_name'];
      return $meta;
    }
    if ($hasCategory === true && $product['brand_name'] !== null) {
      $meta[2] = $product['brand_name'];
    }
    return $meta;
  }

  private static function excerpt($text) { //TODO
    /*
     * 0 => a,b,c,d,！
     * 1 => a1,b1,；,c1,d1,。
     * 2 => a,b1,c2,d1,。
     **/
    //从上到下抽取 text
    //边抽取，边挑选，满足条件后停止
    //在描述里，每个关键字最多重复两次
    //保证没有重复的描述，如果抽取的描述重复，用后面的描述代替前面的描述，显示默认摘要
    //
    return $text;
  }

  private static function highlight($text, $keywordList) { //TODO
    return htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');
  }

  private static function renderMetaList() { //TODO
     
  }
}