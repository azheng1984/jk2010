<?php
class SearchBreadcrumbScreen {
  private static $propertyPathList;

  public static function render() {
    $list = self::buildList();
    echo '<div id="breadcrumb"><h1>';
    $last = count($list) - 1;
    for ($index = 0; $index <= $last; ++$index) {
      $section = $list[$index];
      foreach ($section as $item) {
        if (isset($item['href']) === false && isset($item['class'])
          && $item['class'] === false) {
          echo $item['text'];
          continue;
        }
        $class = isset($item['class']) ? ' class="'.$item['class'].'"' : '';
        if (isset($item['href']) === false) {
          echo '<span', $class, '>', $item['text'], '</span>';
          continue;
        }
        echo '<a', $class, ' href="', $item['href'], '" rel="nofollow">',
          $item['text'], '</a>';
      }
      if ($index !== $last) {
        echo '<span class="delimiter"></span>';
      }
    }
    echo '</h1></div>';
  }

  private static function buildList() {
    $list = array(array(array(
      'text' => htmlentities($GLOBALS['QUERY']['name'], ENT_NOQUOTES, 'UTF-8'),
      'class' => false
    )));
    if (isset($GLOBALS['IS_RECOGNITION'])) {
      $list[0][] = array(
        'text' => '同款',
        'href' => '/'.str_replace('"', '%22', substr($GLOBALS['PATH'], 3))
          .$GLOBALS['QUERY_STRING'],
        'class' => 'tag'
      );
      unset($list[0][0]['class']);
    }
    if (isset($GLOBALS['CATEGORY']) === false) {
      return $list;
    }
    $list[0][0]['href'] = '..'.$GLOBALS['QUERY_STRING'];
    $list[] = array(array(
      'text' => '分类: '
        .htmlentities($GLOBALS['CATEGORY']['name'], ENT_NOQUOTES, 'UTF-8')
    ));
    if (isset($GLOBALS['PROPERTY_LIST']) === false) {
      return $list;
    }
    $list[1][0]['href'] = $list[0][0]['href'];
    $list[0][0]['href'] = '../'.$list[0][0]['href'];
    $list[2] = array();
    foreach ($GLOBALS['PROPERTY_LIST'] as $property) {
      $list[2][] = array(
        'text' =>
          htmlentities($property['key']['name'], ENT_NOQUOTES, 'UTF-8').':'
      );
      foreach ($property['value_list'] as $value) {
        $text = $value['name'];
        if (mb_strlen($text, 'UTF-8') > 60) {
          $text = mb_substr($text, 0, 60, 'UTF-8').'…';
        }
        $list[2][] = array(
          'text' => htmlentities($text, ENT_NOQUOTES, 'UTF-8'),
          'href' => self::buildHref($property['key']['path'], $value['path'])
            .$GLOBALS['QUERY_STRING'],
          'class' => 'tag'
        );
      }
    }
    return $list;
  }

  private static function buildHref($propertyKeyPath, $propertyValuePath) {
    if (self::$propertyPathList === null) {
      self::$propertyPathList = array();
      foreach ($GLOBALS['PROPERTY_LIST'] as $property) {
        $valuePathList = array();
        foreach ($property['value_list'] as $value) {
          $valuePathList[] = $value['path'];
        }
        self::$propertyPathList[$property['key']['path']] = array(
          'path' => implode('&', $valuePathList),
          'value_path_list' => $valuePathList
        );
      }
    }
    $pathList = array();
    foreach (self::$propertyPathList as $keyPath => $item) {
      if ($propertyKeyPath !== $keyPath) {
        $pathList[] = $keyPath.'='.$item['path'];
        continue;
      }
      if (count($item['value_path_list']) === 1) {
        continue;
      }
      $valuePathList = array();
      foreach ($item['value_path_list'] as $valuePath) {
        if ($propertyValuePath !== $valuePath) {
          $valuePathList[] = $valuePath;
        }
      }
      $pathList[] = $keyPath.'='.implode('&', $valuePathList);
    }
    if (count($pathList) === 0) {
      return '..';
    }
    return str_replace('"', '%22', '../'.implode('&', $pathList).'/');
  }
}