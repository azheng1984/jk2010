<?php
class ScreenHeader {
  public function render() {
    echo '<div id="logo"><a href="/">甲壳</a></div>';
    $currentCategory = isset($_GET['category']) ? $_GET['category'] : 'home';
    echo '<div id="category">';
    $this->renderCategoryList($currentCategory);
    echo '</div>';
  }

  private function renderCategoryList($currentCategory) {
    echo '<div class="content">';
    echo '<a ';
    if ($currentCategory === 'home') {
      echo 'class="current" ';
    }
    echo 'href="/">首页</a> ';
    foreach ($_ENV['category'] as $key => $value) {
      echo ' <a ';
      if ($key === $currentCategory) {
        echo 'class="current" ';
      }
      echo 'href="/'.$key.'/">'.$value[0].'</a>';
    }
    echo '</div>';
  }
}