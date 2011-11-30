<?php
class SearchJson {
  public function render() {
    echo '<h2>';
    if (!isset($GLOBALS['URI']['CATEGORY'])) {
      echo '<div id="breadcrumb"><img src="/tag.png" /> <span class="first">分类</span></div>';
    } else {
      if (!isset($_GET['anchor'])) {
        echo '<div id="breadcrumb"><img src="/tag.png" /> <a class="first" href="..">分类</a> &rsaquo; <span>',
          $GLOBALS['URI']['CATEGORY']['name'].'</span></div>';
      } else {
        echo '<div id="breadcrumb"><img src="/tag.png" /> <a class="first" href="">分类</a> &rsaquo;'
          ,' <a href="'.'">'.$this->category['name'].'</a> &rsaquo; <span>'.$this->key['key'].'</span></div>';
      }
    }
    echo '</h2>';
    if (!isset($GLOBALS['URI']['CATEGORY'])) {
      $this->renderCategories();
    } elseif (!isset($_GET['anchor'])) {
      $this->renderKeys();
    } else {
      $this->renderValues();
    }
  }

  private function renderCategories() {
    $categories = CategorySearch::search($GLOBALS['URI']['QUERY']);
    echo '<ol>';
    if ($categories !== false && $categories['total_found'] !== 0) {
      foreach ($categories['matches'] as $item) {
        $category = DbCategory::get($item['attrs']['@groupby']);
        echo '<li><a href="'.$category['name'].'/">'.$category['name'].'</a> <span> x '.$item['attrs']['@count'].'</span></li>';
      }
    }
    echo '</ol>';
  }

  private function renderKeys() {
    $properies = KeySearch::search($GLOBALS['URI']['QUERY'], $GLOBALS['URI']['CATEGORY']);
    echo '<ol id="key_list">';
    foreach ($properies['matches'] as $item) {
      $property = DbProperty::getByKeyId($item['attrs']['@groupby']);
      echo '<li><a class="key" href="#'.$property['key'].'">'.$property['key'].'</a></li>';
    }
    echo '</ol>';
  }

  private function renderValues() {
    $properies = ValueSearch::search($GLOBALS['URI']['QUERY'], $this->category, $this->key);
    echo '<ol>';
    foreach ($properies['matches'] as $item) {
      $property = DbProperty::getByValueId($item['attrs']['@groupby']);
      echo '<li><a href="'.$this->key['key'].'='.$property['value'].'/">'.$property['value'].'</a> <span>'.$item['attrs']['@count'].'</span></li>';
    }
    echo '</ol>';
  }
}