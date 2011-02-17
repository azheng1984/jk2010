<?php
class DocumentScreen {
  private $databaseIndex;
  private $categoryUrlName;

  public function __construct() {
    if (!is_numeric($_GET['id'])) {
      throw new NotFoundException;
    }
    if (!isset($_ENV['category'][$_GET['category']])) {
      throw new NotFoundException;
    }
    $this->categoryUrlName = $_GET['category'];
    $db = new DocumentDb((int)$_GET['database_index']);
    $connection = $db->getConnection();
    $statement = $connection->prepare("select * from tech_document where id=?");
    $statement->execute(array($_GET['id']));
    $this->cache = $statement->fetch(PDO::FETCH_ASSOC);
    if ($this->cache === false) {
      throw new NotFoundException;
    }
    $this->databaseIndex = $_GET['database_index'];
  }

  public function render() {
    $title = "{$this->cache['title']}-甲壳";
    $wrapper = new ScreenWrapper($this, $title, new HtmlMeta($this->cache['description'], $this->cache['keywords']));
    $wrapper->render();
  }

  public function renderContent() {
    echo '<div id="document">';
    echo '<h1>'.$this->cache['title'].'</h1>';
    $this->renderDescription();
    $this->renderImage();
    $meta = new DocumentMetaScreen;
    $meta->render($this->cache);
    $this->renderSourceLink();
    $this->renderRelated();
    $this->renderDocumentListLink();
    echo '</div>';
  }

  private function renderDescription() {
    echo '<div class="description">'.$this->cache['description'].'</div>';
  }

  private function renderImage() {
    if (isset($this->cache['image_url_prefix'])) {
      $title = "《{$this->cache['title']}》的图片";
      echo ' <div class="image"><img title="'.$title.'" alt="'.$title.'" src="'.$this->cache['image_url_prefix'].'-'.$this->cache['url_name'].'.jpg" /></div>';
    }
  }

  private function renderSourceLink() {
    echo '<div class="source_link">';
    if (isset($_ENV['source'][$this->cache['source_id']][1])) {
      echo '<img src="/image/source/'.$_ENV['source'][$this->cache['source_id']][1].'" /> ';
    }
    echo $this->cache['source_url'].' <a target="_blank" href="http://'.$this->cache['source_url'].'">浏览</a></div>';
  }

  private function renderDocumentListLink() {
    $url = '/'.$this->categoryUrlName.'/'.$this->databaseIndex.'-'.$this->cache['list_page_id'].'/#'.$this->cache['url_name'];
    echo "<div class=\"back\"><a href=\"$url\">返回《{$this->cache['title']}》所在的列表</a></div>";
  }

  private function renderRelated() {
    echo '<div id="related">';
    echo '<span class="red_title">相关热点</span>';
    $tmp = substr($this->cache['related_cache'], 1, strlen($this->cache['related_cache']) - 2);
    $items = explode('";"', $tmp);
    foreach ($items as $row) {
      $this->renderRelatedItem($row);
    }
    echo '</div>';
  }

  private function renderRelatedItem($row) {
    echo '<span class="item">';
    $columns = explode('","', $row);
    echo '<a href="'.$columns[0].'-'.$columns[3].'.html">'.$columns[1].'</a>';
    echo '</span>';
    if (!empty($columns[5])) {
      echo '<img src="'.$columns[5].'-'.$columns[3].'.jpg" title='.$columns[4].'" alt="'.$columns[4].'" />';
    } else {
      echo $columns[4];
    }
  }
}