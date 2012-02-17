<?php
class SearchProductListScreen {
  public static function render($searchScreen) {
    $keywordList =
      explode(' ', SegmentationService::execute($GLOBALS['QUERY']['name']));
    $metaList = array();
    $index = 0;
    $hasCategory = isset($GLOBALS['CATEGORY']);
    echo '<ol>';
    foreach ($GLOBALS['SEARCH_RESULT']['matches'] as $id => $result) {
      $product = DbProduct::get($id);
      $merchant = DbMerchant::get($product['merchant_id']);
      $title = self::highlight($product['title'], $keywordList);
      $specification = self::highlight(
        self::excerpt($product['property_list'], $keywordList), $keywordList
      );
      echo '<li>',
        '<div class="image"><a href="" target="_blank" rel="nofollow">',
        '<img alt="', $title, '" src="http://img.dev.huobiwanjia.com/',
        $product['id'], '.jpg"/></a></div>',//image
        '<h3><a href="" target="_blank" rel="nofollow">',
        $title, '</a></h3>',//title
        '<div class="price">&yen;<span>',
        $product['lowest_price_x_100']/100, '</span></div>',//price
        '<p>', $specification, '&hellip;</p>',//specification
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

  private static function excerpt($propertyList, $keywordList) { //TODO
    print_r($keywordList);
    foreach ($keywordList as $keyword) {
      $matches = array();
      $propertyList = "\n,适用,人群,:,1-6,岁;,儿x童x fasdf,\n,材料,:,PE,\n,型号,:,233xx,\n,尺寸,:,长,:,27.6cm, ,宽,:,21.3cm, ,高:6.5cm,\n,适用人群,:,儿童,\n".$propertyList;
      for ($i = 0; $i < 1000; ++$i) {
        preg_match('{\S.*,'.$keyword.',.*\S}', ','.$propertyList.',', $matches);
      }
      print_r($matches);
      exit;
      
    }
    //$propertyList = explode(';', $propertyList);
    return $propertyList;
  }

  private static function highlight($text, $keywordList) { //TODO
    //return htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');
  }

  private static function renderMetaList() { //TODO
  }
}