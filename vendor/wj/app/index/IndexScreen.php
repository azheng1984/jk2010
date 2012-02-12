<?php
class IndexScreen extends Screen {
  private $category;
  private $page;

  public function __construct() {
    $depth = count($GLOBALS['PATH_SECTION_LIST']);
    $this->parseCategory($depth);
    $this->parsePage($depth);
  }

  protected function renderHtmlHeadContent() {
    $title = '分类';
    if ($this->category !== null) {
      $title = $this->category['name'];
    }
    if ($this->page != 1) {
      $title .= '('.$this->page.')';
    }
    echo '<title>', $title, '-货比万家</title>';
    $this->addCssLink('index');
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="index">';
    $this->renderBreadcrumb();
    $this->renderLinkTable();
    $this->renderPagination();
    echo '</div>';
  }

  private function parseCategory($depth) {
    /* /+i/ */
    if ($depth === 3) {
      $this->category = null;
      return;
    }
    /* /+i/category/ */
    $this->category = DbCategory::getByName(
        urldecode($GLOBALS['PATH_SECTION_LIST']['2'])
    );
    if ($this->category === false || $depth > 4) {
      throw new NotFoundException;
    }
  }
  
  private function parsePage($depth) {
    $path = $GLOBALS['PATH_SECTION_LIST'][$depth - 1];
    if ($path === '') {
      $this->page = 1;
      return;
    }
    if (!is_numeric($path) || $path < 2) {
      throw new NotFoundException;
    }
    $this->page = intval($path);
  }

  private function renderBreadcrumb() {
    echo '<div id="breadcrumb">',
      '<span class="home"><a href="/"><img alt="首页" src="/+/img/home.',
      Asset::getMd5('/home.png'),'.png" /></a></span> ';
    if ($this->category !== null) {
      echo '<span><a href="/+i/">分类</a></span> <h1>',
        $this->category['name'], '</h1></div>';
      return;
    }
    echo '<h1>分类</h1></div>';
  }

  private function renderLinkTable() {
    $linkList = $this->getLinkList();
    $amount = count($linkList);
    $index = 0;
    echo '<table>';
    for ($row = 0; $row < 20; ++$row) {
      echo '<tr>';
      for ($column = 0; $column < 5; ++$column, ++$index) {
        if ($index < $amount) {
          echo '<td><a href="', $linkList[$index]['href'], '">',
            $linkList[$index]['text'], '</a></td>';
          continue;
        }
        echo '<td class="empty"></td>';
      }
      echo '</tr>';
      if ($index >= $amount) {
        break;
      }
    }
    echo '</table>';
  }

  private function getLinkList() {
    $result = array();
    if ($this->category === null) {
      $categoryList = DbCategory::getList($this->page);
      foreach ($categoryList as $category) {
        $result[] = array(
            'text' => $category['name'], 'href' => $category['name'].'/'
        );
      }
      return $result;
    }
    $queryList = DbQuery::getList($this->category['id'], $this->page);
    foreach ($queryList as $query) {
      $result[] = array(
          'text' => $query['name'], 'href' => '/'.$query['name'].'/'
      );
    }
    return $result;
  }

  private function renderPagination() {
    PaginationScreen::render($this->page, $this->getAmount(), 100);
  }

  private function getAmount() {
    if ($this->category !== null) {
      return $this->category['query_amount'];
    }
    return 1000;
  }
}