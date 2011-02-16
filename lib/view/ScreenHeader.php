<?php
class ScreenHeader {
  public function render() {
    echo '    <div id="logo"><a href="/">甲壳 - 发现热点，驱动潮流！</a></div>', "\n";
    $currentCategory = isset($_GET['category']) ? $_GET['category'] : 'home';
    echo '<div id="category">';
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