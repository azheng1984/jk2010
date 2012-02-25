<?php
class SearchBreadcrumbScreen {
  public static function render() {
    $recognitionSection = '';
    if (isset($GLOBALS['IS_RECOGNITION'])) {
      $recognitionSection = ' <span class="recognition">（同款）</span>';
    }
    $list = array(
      htmlentities($GLOBALS['QUERY']['name'], ENT_NOQUOTES, 'UTF-8')
    );
    if (isset($GLOBALS['CATEGORY']) === false) {
      echo '<div id="breadcrumb"><h1>', $list[0], $recognitionSection,
        '</h1></div>';
      return;
    }
    $list[] = '分类: '.htmlentities(
      $GLOBALS['CATEGORY']['name'], ENT_NOQUOTES, 'UTF-8'
    );
    $pathList = array('..');
    if (isset($GLOBALS['PROPERTY_LIST'])) {
      $propertySectionList = array();
      foreach ($GLOBALS['PROPERTY_LIST'] as $property) {
        $propertySection =
          htmlentities($property['key']['name'], ENT_NOQUOTES, 'UTF-8').':';
        $valueSectionList = array();
        foreach ($property['value_list'] as $value) {
          $valueSectionList[] = '<span class="value">'
            .htmlentities($value['name'], ENT_NOQUOTES, 'UTF-8').'</span>';
        }
        $propertySection .= implode(' ', $valueSectionList);
        $propertySectionList[] = $propertySection;
      }
      $list[] = implode(' ', $propertySectionList);
      $pathList[] = '..';
    }
    echo '<div id="breadcrumb"><h1>';
    $lastIndex = count($list) - 1;
    for ($index = 0; $index < $lastIndex; ++$index) {
      echo '<span><a href="', implode('/', $pathList),
        $GLOBALS['QUERY_STRING'], '"';
      if ($index !== 0 || $GLOBALS['QUERY_STRING'] !== ''
        || $recognitionSection !== '') {
        echo ' rel="nofollow"';
      }
      echo '>', $list[$index], '</a>';
      if ($index === 0) {
        echo $recognitionSection;
      }
      echo '</span> ';
      array_pop($pathList);
    }
    echo $list[$lastIndex], '</h1></div>';
  }
}