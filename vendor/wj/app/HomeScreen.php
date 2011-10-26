<?php
class HomeScreen extends Screen {
  public function renderContent() {
  	echo '<div style="padding:40px;color:white;background:red;font-size:20px;">★</div>';
    $categories = DbCategory::getList();
    echo '<div id="home"><ul id="category_list" class="home_category_list">';
    for($i = 1; $i < 10; ++$i) {
      foreach ($categories as $category) {
        echo '<li><div class="bull">&bull;</div><h2><a href="'.urlencode($category['name']).'/">'
          .$category['name'].'</a></h2>';
        $children = DbCategory::getList($category['id']);
        if (count($children) !== 0) {
          echo '<div class="children';
          if ($i === 9) {
            echo ' last';
          }
          echo '">';
          echo implode(' ', $this->getChildLinks($category, $children)).' &hellip;</div>';
        }
        echo '</li>';
      }
    }
    echo '</ul></div><script>document.getElementById("search_input").focus()</script>';
  }

  private function getChildLinks($category, $children) {
    $result = array();
    $parentLink = urlencode($category['name']).'/';
    for($i = 1; $i < 12; ++$i) {
      foreach ($children as $child) {
        $result[] = '<a href="'.$parentLink.urlencode($child['name']).'/">'.$child['name'].'</a>';
      }
    }
    return $result;
  }
}