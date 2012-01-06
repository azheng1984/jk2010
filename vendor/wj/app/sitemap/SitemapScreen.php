<?php
class SitemapScreen extends Screen {
  private $page = '1';
  private $amount;
  private $linkList = array();

  public function __construct() {
    if (isset($GLOBALS['URI']['PAGE'])) {
      $this->page = $GLOBALS['URI']['PAGE'];
    }
    if ($GLOBALS['URI']['LIST_TYPE'] === 'category') {
      $categoryList = DbCategory::getList($this->page);
      foreach ($categoryList as $category) {
        $this->linkList[] = array(
          'text' => $category['name'], 'href' => $category['name'].'/'
        );
      }
      $this->amount = 500;
      return;
    }
    $queryList = DbQuery::getList(
      $GLOBALS['URI']['CATEGORY']['id'], $this->page
    );
    foreach ($queryList as $query) {
      $this->linkList[] = array(
        'text' => $query['name'], 'href' => '/'.$query['name'].'/'
      );
    }
  }

  protected function renderHeadContent() {
    $pageSection = '';
    if ($this->page != 1) {
      $pageSection = ' ('.$this->page.')';
    }
    $title = '分类'.$pageSection;
    if (isset($GLOBALS['URI']['CATEGORY'])) {
      $title = $GLOBALS['URI']['CATEGORY']['name'].$pageSection.' - 分类';
    }
    echo '<title>', $title, ' - 货比万家</title>';
    $this->renderCssLink('sitemap');
  }

  protected function renderBodyContent() {
    echo '<div id="sitemap">';
    $this->renderBreadcrumb();
    $this->renderTable();
    $this->renderPagination();
    echo '</div>';
  }

  private function renderBreadcrumb() {
    echo '<div id="breadcrumb">',
      '<span class="home"><a href="/"><img alt="首页" src="/+/img/home.',
      Asset::getMd5('/home.png'),'.png" /></a></span> ';
    if (isset($GLOBALS['URI']['CATEGORY'])) {
      echo '<span><a href="/+i/">分类</a></span> <h1>',
        $GLOBALS['URI']['CATEGORY']['name'], '</h1></div>';
      return;
    }
    echo '<h1>分类</h1></div>';
  }

  private function renderTable() {
    $amount = count($this->linkList);
    $index = 0;
    echo '<table>';
    for ($i = 0; $i < 12; ++$i) {
      echo '<tr>';
      for ($j = 0; $j < 5; ++$j) {
        echo '<td';
        if ($index >= $amount) {
          echo ' class="empty"';
        }
        echo '>';
        if ($index < $amount) {
          echo '<a href="', $this->linkList[$index]['href'], '">',
            $this->linkList[$index]['text'], '</a>';
        }
        echo '</td>';
        ++$index;
      }
      echo '</tr>';
      if ($index >= $amount) {
        break;
      }
    }
    echo '</table>';
  }

  private function renderPagination() {
    PaginationScreen::render($this->amount, 100, '');
  }
}