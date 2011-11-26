<?php
class SearchJson {
  public function render() {
    echo '<div id="filter"><h2>';
    if ($this->category === false) {
      echo '<div id="breadcrumb">标签: <span class="first">分类</span></div>';
    } else {
      if ($this->key === false) {
        echo '<div id="breadcrumb">标签: <a class="first" href="">分类</a> &rsaquo; <span>',
          $this->category['name'].'</span></div>';
      } else {
        echo '<div id="breadcrumb">标签: <a class="first" href="">分类</a> &rsaquo;'
          ,' <a href="'.'">'.$this->category['name'].'</a> &rsaquo; <span>'.$this->key['key'].'</span></div>';
      }
    }
    echo '</h2>';
    if ($this->category === false) {
      $this->renderCategories();
    } elseif ($this->key === false) {
      $this->renderKeys();
    } else {
      $this->renderValues();
    }
    echo '</div>';
  }

  private function renderCategories() {
    $categories = CategorySearch::search($this->query);
    echo '<ol>';
    foreach ($categories['matches'] as $item) {
      $category = DbCategory::get($item['attrs']['@groupby']);
      echo '<li><a href="'.$category['name'].'/">'.$category['name'].'</a> <span>'.$item['attrs']['@count'].'</span></li>';
    }
    echo '</ol>';
  }

  private function renderKeys() {
    $properies = KeySearch::search($this->query, $this->category);
    echo '<ol id="key_list">';
    foreach ($properies['matches'] as $item) {
      $property = DbProperty::getByKeyId($item['attrs']['@groupby']);
      echo '<li><span>+</span><a href="?t='.$property['key'].'">'.$property['key'].'</a></li>';
    }
    echo '</ol>';
  }

  private function renderValues() {
    $properies = ValueSearch::search($this->query, $this->category, $this->key);
    echo '<ol>';
    foreach ($properies['matches'] as $item) {
      $property = DbProperty::getByValueId($item['attrs']['@groupby']);
      echo '<li><a href="'.$this->key['key'].'='.$property['value'].'/">'.$property['value'].'</a> <span>'.$item['attrs']['@count'].'</span></li>';
    }
    echo '</ol>';
  }
}