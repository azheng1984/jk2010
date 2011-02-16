<?php
class DocumentListScreen implements IContent {
  private $cache;

  public function render() {
    $connection = new PDO('mysql:host=localhost;dbname=jiakr', 'root', 'a841107!',
     array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    $statement = $connection->prepare("select * from tech_document_list where id=?");
    if (!is_numeric($_GET['page'])) {
      throw new NotFoundException;
    }
    $statement->execute(array($_GET['page']));
    $this->cache = $statement->fetch(PDO::FETCH_ASSOC);
    if ($this->cache === false) {
      throw new NotFoundException;
    }
    $title = "科技存档第{$_GET['page']}页-甲壳";
    $wrapper = new ScreenWrapper($this, $title, new HtmlMeta);
    $wrapper->render();
  }

  public function renderContent() {
    $this->renderPageNavigator();
    $tmp = substr($this->cache['content_cache'], 1, strlen($this->cache['content_cache']) - 2);
    $items = explode('";"', $tmp);
    foreach ($items as $row) {
      $columns = explode('","', $row);
      echo '<div>';
      $url = '/tech/1-1/'.$columns[0]."-{$columns[1]}.html";
      echo '<div><a href="', $url, '">', $columns[2], "</a>";
      if (!empty($columns[5])) {
        $imageUrl = '/tech/1-1/'.$columns[5]."_s-{$columns[1]}.jpg";
        echo ' <span class="image"><img alt="'.$columns[2].'" title="'.$columns[2].'" src="'.$imageUrl.'" /></span>';
      }
      echo $columns[3];
      if (!empty($columns[6])) {
        echo ' <span class="time">'.$columns[6].'</span>';
      }
      if (!empty($columns[7])) {
        echo ' <span class="place">'.$columns[7].'</span>';
      }
      if (!empty($columns[8])) {
        echo ' <span class="people">'.$columns[8].'</span>';
      }
      echo ' - <span class="source">', $_ENV['source'][(int)$columns[4]], '</span>';
      echo '</div>';
    }
    $this->renderPageNavigator();
  }

  private function renderPageNavigator() {
    echo '<div>';
    if ((int)$this->cache['id'] === $_ENV['category'][$_GET['category']][1]) {
      echo '上一页';
    } else {
      echo '<a href ="/tech/1-'.($this->cache['id'] + 1).'/">上一页</a>';
    }
    echo ' | ';
    if ($this->cache['id'] === '1') {
      echo '下一页';
    } else {
      echo '<a href ="/tech/1-'.($this->cache['id'] - 1).'/">下一页</a>';
    }
    echo '</div>';
  }
}