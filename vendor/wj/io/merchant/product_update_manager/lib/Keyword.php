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

  public static function isUpdated($product, $replacementColumnList, $shoppingCategoryId, $shoppingPropertyTextList) {
    if (isset($replacementColumnList['title'])
        || isset($replacementColumnList['category_name'])
        || isset($replacementColumnList['property_list'])) {
      $keywordList = explode(' ', $product['keyword_list']);
      $keywordListByKey = array();
      foreach ($keywordList as $keyword) {
        $keywordListByKey[$keyword] = true;
      }
      $keywords = self::getList($product, $shoppingPropertyTextList);
      $currentKeywordList = explode(' ', $keywords);
      $isUpdated = false;
      foreach ($currentKeywordList as $item) {
        if (isset($keywordListByKey[$item])) {
          unset($keywordListByKey[$itme]);
          continue;
        }
        $isUpdated = true;
        break;
      }
      if (count($keywordListByKey) !== 0) {
        $isUpdated = true;
      }
      if ($isUpdated) {
        $updateColumnList['keyword_list'] = $keywords;
      }
    }
  }
}