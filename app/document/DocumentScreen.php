<?php
class DocumentScreen {
    private $id;
    private $cache;
    private $databaseIndex;
    private $categoryUrlName;

    public function __construct() {
        if (!isset($_ENV['category'][$_GET['category']])) {
            throw new NotFoundException;
        }
        if (!is_numeric($_GET['database_index']) ||
            !isset($_ENV['document_database'][(int)$_GET['database_index']])) {
            throw new NotFoundException;
        }
        if (!is_numeric($_GET['id'])) {
            throw new NotFoundException;
        }
        $this->id = $_GET['id'];
        $this->databaseIndex = $_GET['database_index'];
        $this->categoryUrlName = $_GET['category'];
        $this->setCache();
        if ($this->cache === false) {
            throw new NotFoundException;
        }
    }

    private function setCache() {
        $db = new DocumentDb($this->databaseIndex);
        $connection = $db->getConnection();
        $statement = $connection->prepare("select * from {$this->categoryUrlName}_document where id=?");
        $statement->execute(array($this->id));
        $this->cache = $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function render() {
        $title = "{$this->cache['title']}_甲壳";
        $meta = new HtmlMeta($this->cache['description'], $this->cache['keywords']);
        $wrapper = new ScreenWrapper($this, $title, $meta);
        $wrapper->render();
    }

    public function renderContent() {
        echo '<div id="document">';
        echo '<h1>',  $this->cache['title'], '</h1>';
        $this->renderDynamicSourceLink();
        $this->renderDescription();
        $this->renderImage();
        $meta = new DocumentMetaScreen;
        $meta->render($this->cache);
        $this->renderRelated();
        $this->renderBackLink();
        echo '</div>';
        $adsense = new AdSenseScreen;
        echo '<div id="recent">';
        $adsense->render('test');
        echo '</div>';
    }

    private function renderDescription() {
        echo '<div class="description">', $this->cache['description'], '</div>';
    }

    private function renderImage() {
        if (isset($this->cache['image_url_prefix'])) {
            $title = "《{$this->cache['title']}》的图片";
            echo ' <div class="image"><img title="', $title, '" alt="', $title,
                '" src="', $this->cache['image_url_prefix'], '-', $this->cache['url_name'], '.jpg" /></div>';
        }
    }

    private function renderDynamicSourceLink() {
        echo '<div class="source_link">';
        if (isset($_ENV['source'][$this->cache['source_id']][1])) {
            echo '<div class="text"><img src="/image/source/', $_ENV['source'][$this->cache['source_id']][1], '" /> ';
        }
        echo $this->cache['source_url'], '</div><div><a target="_blank" href="/source'.$_SERVER['REQUEST_URI'].'" rel="nofollow">瞄一眼</a></div></div>';
    }

    private function renderBackLink() {
        $url = '/'.$this->categoryUrlName.'/'.$this->databaseIndex.'-'
            .$this->cache['list_page_id'].'/#'.$this->cache['url_name'];
        echo "<div class=\"back\"><a target=\"_blank\" href=\"http://{$this->cache['source_url']}\">去".$_ENV['source'][$this->cache['source_id']][0]."“瞄一眼”这个热点</a>",
            " | <a class=\"to_list\" href=\"$url\">返回《{$this->cache['title']}》所在的列表</a></div>";
    }

    private function renderRelated() {
        echo '<div id="related">';
        echo '<span class="red_title">相关热点</span>';
        $tmp = substr($this->cache['related_cache'], 1,
            strlen($this->cache['related_cache']) - 2);
        $items = explode('";"', $tmp);
        foreach ($items as $row) {
            $this->renderRelatedItem($row);
        }
        echo '</div>';
    }

    private function renderRelatedItem($row) {
        $columns = explode('","', $row);
        echo '<span class="link"><a href="',
            $columns[0], '-', $columns[3], '.html">', $columns[1], '</a></span>';
        if (!empty($columns[5])) {
            echo '<img src="', $columns[5], '-'.$columns[3], '.jpg" title=',
                $columns[4], '" alt="', $columns[4], '" />';
        } else {
            echo $columns[4];
        }
    }
}
