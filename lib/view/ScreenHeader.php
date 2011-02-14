<?php
class ScreenHeader {
  public function render() {
    echo '    <div>甲壳 - 发现热点，驱动潮流！</div>', "\n";
    foreach ($_ENV['category'] as $key => $value) {
      echo ' <a href="/'.$key.'/">'.$value.'</a>';
    }
  }
}