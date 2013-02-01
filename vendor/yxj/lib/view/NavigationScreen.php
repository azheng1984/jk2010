<?php
class NavigationScreen {
  private static $config = array(
    'browse' => '浏览',
    'discussion' => '讨论',
    'member' => '成员',
    //'download' => '下载',
  );

  public static function render() {
    echo '<ul>';
    foreach (self::$config as $key => $value) {
      if ($GLOBALS['NAVIGATION_MODULE'] === $key) {
        echo '<li>', $value, '</li>';
        continue;
      }
      echo '<li><a href="/article-', $GLOBALS['ARTICLE_ID'];
      if ($key !== 'browse') {
        echo '/', $key;
      }
      echo '/">', $value, '</a></li>';
    }
    echo '</ul>';
  }
}