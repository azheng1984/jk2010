<?php
class CategoryScreen implements IContent {
  public function render() {
    if (!isset($_ENV[$_GET['category']])) {
      throw new NotFoundException;
    }
    //header('Last-Modified: '.gmdate('D, d M Y 01:01:01',$time).' GMT');
    if ($_GET['category'] == 'home') {
      $title = "甲壳网 - 发现热点，驱动潮流";
    } else {
      $title = "{$_ENV['category'][$_GET['category']]} - 甲壳网";
    }
    $wrapper = new ScreenWrapper($this, $title);
    $wrapper->render();
  }

  public function renderContent() {
    $cache = require DATA_PATH."'category/{$_ENV[$_GET['category']]}.php";
    echo '<div>热点话题</div>';
    foreach ($cache['hot'] as $item) {
      echo '<div><a href="', $item['url'], '">', $item['title'], "</a>";
      echo $item['description'];
      if (isset($item['time'])) {
        echo '<span class="time">'.$item['time'].'</span>';
      }
      if (isset($item['place'])) {
        echo '<span class="place">'.$item['place'].'</span>';
      }
      if (isset($item['people'])) {
        echo '<span class="people">'.$item['people'].'</span>';
      }
      echo '</div>';
    }
    echo '<div>最新发布</div><ul>';
    foreach ($cache['recent'] as $item) {
      echo '<li><a href="', $item['url'], '">', $item['title'],
           "</a> - {$_ENV[$item['source_id']]}</li>";
    }
    echo '</ul>';
  }
}