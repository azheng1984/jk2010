<?php
class DocumentListScreen implements IContent {
  private $cache;
  private $databaseIndex;
  private $categoryUrlName;
  private $pageAmount;
  private $page;

  public function __construct() {
    if (!isset($_ENV['category'][$_GET['category']])) {
      throw new NotFoundException;
    }
    if (!is_numeric($_GET['database_index'])
     || !isset($_ENV['document_database'][(int)$_GET['database_index']])) {
      throw new NotFoundException;
    }
    $this->databaseIndex = $_GET['database_index'];
    $this->categoryUrlName = $_GET['category'];
    $this->categoryInfo = $_ENV['category'][$_GET['category']];
    $this->pageAmount = $this->categoryInfo[1];
    $this->page = $_GET['page'];
    if (!is_numeric($this->page)
     || $this->page < 1
     || $this->page > $this->pageAmount) {
      throw new NotFoundException;
    }
    $this->setCache();
    if ($this->cache === false) {
      throw new NotFoundException;
    }
  }

  private function setCache() {
    $db = new DocumentDb($this->databaseIndex);
    $connection = $db->getConnection();
    $statement = $connection->prepare("select * from {$this->categoryUrlName}_document_list where id=?");
    $statement->execute(array($this->pageAmount));
    $this->cache = $statement->fetch(PDO::FETCH_ASSOC);
  }

  public function render() {
    $title = "第{$this->page}页-甲壳科技";
    $wrapper = new ScreenWrapper($this, $title);
    $wrapper->render();
  }

  public function renderContent() {
    echo '<div id="list">';
    $tmp = substr($this->cache['content_cache'], 1, strlen($this->cache['content_cache']) - 2);
    $items = explode('";"', $tmp);
    $summary = new DocumentSummaryScreen;
    foreach ($items as $row) {
      $columns = explode('","', $row);
      $item = array(
        'id' => $columns[0],
        'url_name' => $columns[1],
        'title'=> $columns[2],
        'description'=> $columns[3],
        'source_id'=> $columns[4],);
      if (!empty($columns[5])) {
        $item['image_url'] = '/'.$this->categoryUrlName.'/1-'.$this->page.'/'.$columns[5]."_s-{$columns[1]}.jpg";
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
      $summary->render($item, true);
    }
    $this->renderNavigator();
    echo '</div>';
    $adsense = new AdSenseScreen;
    echo '<div id="recent">';
    $adsense->render('test');
    echo '</div>';
  }

  private function renderNavigator() {
    echo '<div id="page">';
    if ((int)$this->cache['id'] === $this->categoryInfo[1]) {
      echo '上一页';
    } else {
      echo '<a href ="/'.$this->categoryUrlName.'/1-'.($this->cache['id'] + 1).'/">上一页</a>';
    }
    echo " 第 {$this->page} 页 ";
    if ($this->cache['id'] === '1') {
      echo '下一页';
    } else {
      echo '<a href ="/'.$this->categoryUrlName.'/1-'.($this->cache['id'] - 1).'/">下一页</a>';
    }
    echo '</div>';
  }
}