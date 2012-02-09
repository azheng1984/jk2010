<?php
class CategoryListContentScreen {
  public static function render($categoryId = 0) {
    $categories = DbCategory::getList($categoryId);
    echo '<ul id="category_list">';
    foreach ($categories as $category) {
      echo '<li>';
      self::renderItem($category);
      echo '</li>';
    }
    echo '</ul>';
  }

  private static function renderItem($category) {
    echo '<div class="item"><a rel="nofollow" href="'.urlencode($category['name']).'/">',
      $category['name'], '</a></div>';
    $children = DbCategory::getList($category['id']);
    //$children = array(array('name' => '笔记本电脑'), array('name' => '数码相机'));
    if (count($children) !== 0) {
      echo '<div class="children">',
        implode(' ', self::getChildLinks($category, $children)),
        ' &hellip;</div>';
    }
  }

  private static function getChildLinks($category, $children) {
    $result = array();
    $parentLink = urlencode($category['name']).'/';
    foreach ($children as $child) {
      $result[] = '<a rel="nofollow" href="'.$parentLink.urlencode($child['name']).'/">'
        .$child['name'].'</a>';
    }
    return $result;
  }
}