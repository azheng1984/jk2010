<?php
class SearchJson {
  public function render() {
      if (!isset($GLOBALS['URI']['CATEGORY']) || isset($_GET['anchor']) && $_GET['anchor'] === '分类') {
      echo '<h2><div id="breadcrumb"><img src="/tag.png" /> <span>分类</span></div></h2>';
    } else {
      if (!isset($_GET['anchor'])) {
        echo '<h2><div id="breadcrumb"><img src="/tag.png" /> <span>属性</span></div></h2>';
      } else {
        $this->category = $GLOBALS['URI']['CATEGORY'];
        $this->key = DbProperty::getKeyByName($GLOBALS['URI']['CATEGORY']['id'], $_GET['anchor']);
      }
    }
    if (!isset($GLOBALS['URI']['CATEGORY']) || isset($_GET['anchor']) && $_GET['anchor'] === '分类') {
      $this->renderCategories();
    } elseif (!isset($_GET['anchor'])) {
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
        echo '<li><a '.$class.'href="'.$categoryUri.'">'.$category['name'].' <span>'.$item['attrs']['@count'].'</span></a> </li>';
      }
    }
    echo '</ol>';
  }

  private function renderKeys() {
    $properies = KeySearch::search($GLOBALS['URI']['QUERY'], $GLOBALS['URI']['CATEGORY']);
    echo '<ol id="key_list">';
    foreach ($properies['matches'] as $item) {
      $property = DbProperty::getByKeyId($item['attrs']['@groupby']);
      echo '<li><span class="key">'.$property['key'].'</span></li>';
    }
    echo '</ol>';
  }

  private function renderValues() {
    $properies = ValueSearch::search($GLOBALS['URI']['QUERY'], $this->category, $this->key);
    echo '<ol id="value_list">';
    foreach ($properies['matches'] as $item) {
      $property = DbProperty::getByValueId($item['attrs']['@groupby']);
      echo '<li><a href="'.$this->key['key'].'='.$property['value'].'/">'.$property['value'].' <span>'.$item['attrs']['@count'].'</span></a><a href="javascript:void(0)" class="delete"></a></li>';
    }
    echo '</ol>';
  }
}