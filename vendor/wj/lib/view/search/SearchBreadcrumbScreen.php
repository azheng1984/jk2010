<?php
class SearchBreadcrumbScreen {
  public static function render() {
    $list = array(htmlentities($GLOBALS['QUERY'], ENT_NOQUOTES, 'UTF-8'));
    if (isset($GLOBALS['CATEGORY']) === false) {
      echo '<h1>', $list[0], '</h1>';
      return;
    }
    //  /+p/category/key=value/?id=389987097
    if (isset($GLOBALS['PRODUCT_RECOGNITION']) === false) {
      echo '<h1><span><a href="">', $list[0], '</a></span> 同款</h1>';
      return;
    }
    $list[] = '分类:'.htmlentities(
        $GLOBALS['CATEGORY']['name'], ENT_NOQUOTES, 'UTF-8'
    );
    $pathList = array('..');
    if (isset($GLOBALS['PROPERTY_LIST'])) {
      $propertySectionList = array();
      foreach ($GLOBALS['PROPERTY_LIST'] as $property) {
        $propertySection =
          htmlentities($property['KEY']['name'], ENT_NOQUOTES, 'UTF-8').':';
        $valueSectionList = array();
        foreach ($property['VALUE_LIST'] as $value) {
          $valueSectionList[] = '<span class="value">'
            .htmlentities($value['name'], ENT_NOQUOTES, 'UTF-8').'</span>';
        }
        $propertySection .= implode(' ', $valueSectionList);
        $propertySectionList[] = $propertySection;
      }
      $list[] = implode(' ', $propertySectionList);
      $pathList[] = '..';
    }
    echo '<h1>';
    $lastIndex = count($list) - 1;
    for ($index = 0; $index < $lastIndex; ++$index) {
      echo '<span><a href="', implode('/', $pathList),
        $GLOBALS['QUERY_STRING'], '"';
      if ($index !== 0 || $GLOBALS['QUERY_STRING'] !== '') {
        echo ' rel="nofollow"';
      }
      echo '>', $list[$index], '</a></span> ';
      array_pop($pathList);
    }
    echo $list[$lastIndex], '</h1>';
  }
}