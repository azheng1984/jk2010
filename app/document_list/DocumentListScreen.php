<?php
class DocumentListScreen implements IContent {
  private $cache;
  private $categoryUrlName;
  private $pageAmount;
  private $page;

  public function __construct() {
    if (!isset($_ENV['category'][$_GET['category']])) {
      throw new NotFoundException;
    }
    $this->categoryUrlName = $_GET['category'];
    $this->categoryInfo = $_ENV['category'][$_GET['category']];
    $this->pageAmount = $this->categoryInfo[1];
    $this->page = $_GET['page'];
    if (!is_numeric($this->page)
     || $this->page < 1
     || $this->page > $this->pageAmount) {
      throw new NotFoundException;
    }
    $connection = new PDO('mysql:host=localhost;dbname=jiakr',
                          'root',
                          'a841107!',
                          array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    $statement = $connection->prepare("select * from {$this->categoryUrlName}_document_list where id=?");
    $statement->execute(array($this->pageAmount));
    $this->cache = $statement->fetch(PDO::FETCH_ASSOC);
    if ($this->cache === false) {
      throw new NotFoundException;
    }
  }

  public function render() {
    $title = "第{$this->page}页-甲壳科技";
    $wrapper = new ScreenWrapper($this, $title, new HtmlMeta);
    $wrapper->render();
  }

  public function renderContent() {
    $tmp = substr($this->cache['content_cache'], 1, strlen($this->cache['content_cache']) - 2);
    $items = explode('";"', $tmp);
    foreach ($items as $row) {
      $columns = explode('","', $row);
      $item = array(
        'id' => $columns[0],
        'url_name' => $columns[1],
        'title'=> $columns[2],
        'description'=> $columns[3],
        'source_id'=> $columns[4],);
      if (!empty($columns[5])) {
        $item['image_url'] = '/tech/1-1/'.$columns[5]."_s-{$columns[1]}.jpg";
      }
      if (!empty($columns[6])) {
        $item['time'] = $columns[6];
      }
      if (!empty($columns[7])) {
        $item['place'] = $columns[7];
      }
      if (!empty($columns[8])) {
        $item['people'] = $columns[8];
      }
      $item['url'] = '/tech/1-1/'.$columns[0]."-{$columns[1]}.html";
      $this->renderHotItem($item);
      //todo: add anchor
    }
    $this->renderNavigator();
  }
  
  private function renderDocumentTitle($item) {
    echo '<div class="title"><a href="', $item['url'], '">',
         $item['title'], "</a></div>";
  }
  
  private function renderDocumentDescription($item) {
    echo '<div class="description">'.$item['description'].'</div>';
  }

  private function renderDocumentMeta($item) {
    echo '<div class="meta">';
    $outputs = array();
    foreach (array('time', 'place', 'people') as $meta) {
      if (isset($item[$meta])) {
        $outputs[] = '<span class="'.$meta.'">'.$item[$meta].'</span>';
      }
    }
    $outputs[] = '<span class="source">'
                .$_ENV['source'][$item['source_id']].'</span>';
    echo implode(' | ', $outputs);
    echo '</div>';
  }

  private function renderHotItemText($item, $hasImage) {
    echo '<div class="text';
    if ($hasImage) {
      echo ' short_text';
    }
    echo '">';
    $this->renderDocumentTitle($item);
    $this->renderDocumentDescription($item);
    $this->renderDocumentMeta($item);
    echo '</div>';
  }

  private function renderHotItem($item) {
    $hasImage = isset($item['image_url']);
    echo '<div class="item">';
    $this->renderHotItemText($item, $hasImage);
    if ($hasImage) {
      echo '<div class="image"><img src="'.$item['image_url'].'" /></div>';
    }
    echo '</div>';
  }

  private function renderNavigator() {
    echo '<div>';
    if ((int)$this->cache['id'] === $this->categoryInfo[1]) {
      echo '上一页';
    } else {
      echo '<a href ="/'.$this->categoryUrlName.'/1-'.($this->cache['id'] + 1).'/">上一页</a>';
    }
    echo " 第 {$_GET['page']} 页 ";
    if ($this->cache['id'] === '1') {
      echo '下一页';
    } else {
      echo '<a href ="/'.$this->categoryUrlName.'/1-'.($this->cache['id'] - 1).'/">下一页</a>';
    }
    echo '</div>';
  }
}