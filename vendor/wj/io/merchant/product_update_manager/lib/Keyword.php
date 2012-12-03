<?php
class Keyword {
  public static function getList($product, $shoppingPropertyTextList) {
    $keywords = $product['title'];
    $keywords .= ' '.$product['category_name'];
    $keywords .= ' '.$shoppingPropertyTextList;
    $keywords = SegmentationService::execute($keywords);
    $list = explode(' ', $keywords);
    return implode(' ', array_unique($list));
  }
}