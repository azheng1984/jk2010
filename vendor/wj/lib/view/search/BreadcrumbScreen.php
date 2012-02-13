<?php
class BreadcrumbScreen {
  public static function render() {
    $list = array(htmlentities($GLOBALS['QUERY'], ENT_NOQUOTES, 'UTF-8'));
    $pathList = array();
    if (isset($GLOBALS['CATEGORY'])) {
      $list[] = '分类:'.htmlentities($GLOBALS['CATEGORY']['name'], ENT_NOQUOTES, 'UTF-8');
      $pathList[] = '..';
    }
    if (isset($GLOBALS['PROPERTY_LIST'])) {
      $propertyList = array();
      foreach ($GLOBALS['PROPERTY_LIST'] as $property) {
        $item = htmlentities($property['KEY']['name'], ENT_NOQUOTES, 'UTF-8').':';
        $valueList = array();
        foreach ($property['VALUE_LIST'] as $value) {
          $valueList[] = '<span class="value">'
            .htmlentities($value['name'], ENT_NOQUOTES, 'UTF-8').'</span>';
        }
        $item .= implode(' ', $valueList);
        $propertyList[] = $item;
      }
      $list[] = implode(' ', $propertyList);
      $pathList[] = '..';
    }
    echo '<h1>';
    $lastIndex = count($list) - 1;
    for ($index = 0; $index < $lastIndex; ++$index) {
      echo '<span><a href="', implode('/', $pathList), '">',
        $list[$index], '</a></span> ';
      array_pop($pathList);
    }
    echo $list[$lastIndex], '</h1>';
  }
}