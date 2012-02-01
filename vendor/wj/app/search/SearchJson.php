<?php
class SearchJson extends EtagView {
  private $category;
  private $key;

  public function __construct() {
    header('Cache-Control: public, max-age=3600');
  }

  public function renderBody() {
    if (isset($_GET['key']) && isset($GLOBALS['URI']['CATEGORY'])) {
      $GLOBALS['URI']['KEY'] = DbPropertyKey::getByName(
        $GLOBALS['URI']['CATEGORY']['id'], $_GET['key']
      );
    }
    if (!isset($GLOBALS['URI']['CATEGORY'])) {
      echo '<h2><span>分类</span></h2>';
    } else {
      if (!isset($GLOBALS['URI']['KEY'])) {
        echo '<h2><span>属性</span></h2>';
      } else {
        $this->category = $GLOBALS['URI']['CATEGORY'];
        $this->key = $GLOBALS['URI']['KEY'];
      }
    }
    if (!isset($GLOBALS['URI']['CATEGORY'])) {
      $this->renderCategories();
    } elseif (!isset($GLOBALS['URI']['KEY'])) {
      $this->renderKeys();
    } else {
      $this->renderValues();
    }
  }

  private function renderCategories() {
    $uri = '/'.urlencode($GLOBALS['URI']['QUERY']).'/';
    $categories = CategorySearch::search($GLOBALS['URI']['QUERY']);
    echo '<ol id="category_list">';
    if ($categories !== false && $categories['total_found'] !== 0) {
      foreach ($categories['matches'] as $item) {
        $category = DbCategory::get($item['attrs']['@groupby']);
        $categoryUri = $uri.$category['name'].'/';
        $class = '';
        if (isset($GLOBALS['URI']['CATEGORY']) && $GLOBALS['URI']['CATEGORY']['name'] == $category['name']) {
          $categoryUri = '#';
          $class = 'class="selected" ';
        }
        //TODO： add sort args
        echo '<li><a '.$class.'href="'.$categoryUri.'">'.$category['name'].' <span>'.$item['attrs']['@count'].'</span></a></li>';
      }
    }
    echo '</ol>';
    echo '<div class="more"><a href="javascript:void(0)">更多分类</a></div>';
  }

  private function renderKeys() {
    $properies = KeySearch::search($GLOBALS['URI']['QUERY'], $GLOBALS['URI']['CATEGORY']);
    echo '<ol id="key_list">';
    foreach ($properies['matches'] as $item) {
      $key = DbPropertyKey::get($item['attrs']['@groupby']);
      echo '<li><div class="key"><span>'.$key['name'].'</span></div></li>';
    }
    echo '</ol>';
  }

  private function renderValues() {
    $properies = ValueSearch::search($GLOBALS['URI']['QUERY'], $this->category, $this->key);
    echo '<ol class="value_list">';
    foreach ($properies['matches'] as $item) {
      $property = DbPropertyValue::get($item['attrs']['@groupby']);
      if (isset($GLOBALS['URI']['PROPERTY_LIST'])
        && isset($GLOBALS['URI']['PROPERTY_LIST'][0]['VALUE_LIST'][0]['id'])
        && $GLOBALS['URI']['PROPERTY_LIST'][0]['VALUE_LIST'][0]['id'] == $item['attrs']['@groupby']) {
        echo '<li><a href="javascript:void(0)" class="selected">'.$property['name'].'</a></li>';
      } else {
        echo '<li><a href="'.$this->key['name'].'='.$property['name'].'/">'.$property['name'].' <span>'.$item['attrs']['@count'].'</span></a></li>';
      }
    }
    echo '</ol>';
  }
}