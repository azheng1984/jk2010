<?php
class CategoryScreen implements IContent {
  public function render() {
    if (!isset($_ENV['category'][$_GET['category']])) {
      throw new NotFoundException;
    }
    //header('Last-Modified: '.gmdate('D, d M Y 01:01:01',$time).' GMT');
    if ($_GET['category'] == 'home') {
      $title = "甲壳 - 发现热点，驱动潮流！";
    } else {
      $title = "{$_ENV['category'][$_GET['category']]} - 甲壳网";
    }
    $wrapper = new ScreenWrapper($this, $title, new HtmlMeta);
    $wrapper->render();
  }

  public function renderContent() {
    $cache = require DATA_PATH."category/{$_GET['category']}.php";
    echo '<div>热点话题</div>';
    foreach ($cache['hot'] as $item) {
      echo '<div><a href="', $item['url'], '">', $item['title'], "</a>";
      if (isset($item['image_url'])) {
        echo ' <span class="image"><img src="'.$item['image_url'].'" />';
      }
      echo $item['description'];
      if (isset($item['time'])) {
        echo ' <span class="time">'.$item['time'].'</span>';
      }
      if (isset($item['place'])) {
        echo ' <span class="place">'.$item['place'].'</span>';
      }
      if (isset($item['people'])) {
        echo ' <span class="people">'.$item['people'].'</span>';
      }
      echo '<span class="source">', $_ENV['source'][$item['source_id']], '</span>';
      echo '</div>';
    }
    echo '<div>最新发布</div><ul>';
    foreach ($cache['recent'] as $item) {
      echo '<li><a href="', $item['url'], '">', $item['title'],
           "</a> - {$_ENV['source'][$item['source_id']]}</li>";
    }
    echo '</ul>';
    if (isset($cache['document_list_url'])) {
      echo '<a href="'.$cache['document_list_url'].'">更多</a>';
    }
  }
}