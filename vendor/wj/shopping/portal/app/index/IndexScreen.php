<?php
class IndexScreen extends Screen {
  private $category;
  private $linkList;

  public function __construct() {
    $depth = count($GLOBALS['PATH_SECTION_LIST']);
    PaginationParser::parsePath($GLOBALS['PATH_SECTION_LIST'][$depth - 1]);
    $this->parseCategory($depth);
    $this->buildLinkList();
    $this->verifyPagination();
  }

  protected function renderHtmlHeadContent() {
    $title = '购物搜索排行';
    if ($this->category !== null) {
      $title .= '/'.$this->category['name'];
    }
    if ($GLOBALS['PAGE'] !== 1) {
      $title .= '('.$GLOBALS['PAGE'].')';
    }
    echo '<title>', $title, ' - 货比万家</title>';
    $this->addCssLink('index');
  }

  protected function renderHtmlBodyContent() {
    $this->renderNavigation();
    echo '<div id="index">';
    $this->renderLinkTable();
    $this->renderPagination();
    echo '</div>';
  }

  private function parseCategory($depth) {
    /* /+top/ */
//     if ($depth === 3) {
//       $this->category = null;
//       return;
//     }
//     /* /+i/category/ */
//     $this->category = Db::getRow('SELECT * FROM category WHERE name = ?',
//       urldecode($GLOBALS['PATH_SECTION_LIST']['2']));
//     if ($this->category === false || $depth > 4) {
//       //throw new NotFoundException;
//     }
  }

  private function renderNavigation() {
    echo '<div id="nav"><div class="content">',
      '<span><a href="/">首页</a></span>购物搜索排行';
    echo '</div></div>';
  }

  private function renderLinkTable() {
    $index = ($GLOBALS['PAGE'] - 1) * 100;
    echo '<table><tr>';
    foreach ($this->linkList as $link) {
      if ($index % 5 === 0 && $index !== 0) {
        echo '</tr><tr>';
      }
      echo '<td><span>', $index + 1,
        '</span> <a href="', $link['href'], '">', $link['text'], '</a></td>';
      ++$index;
    }
    if ($index % 5 !== 0 && $index > 5) {
      $colspan = 5 - $index % 5;
      $colspanAttribute = $colspan === 1 ? '' : ' colspan="'.$colspan.'"';
      echo '<td', $colspanAttribute, '></td>';
    }
    echo '</tr></table>';
  }

  private function buildLinkList() {
    $result = array();
    $offset = ($GLOBALS['PAGE'] - 1) * 100;
//     if ($this->category === null) {
//       $categoryList = Db::getAll(
//         'SELECT * FROM category ORDER BY popularity_rank LIMIT '.$offset.', 100'
//       );
//       foreach ($categoryList as $category) {
//         $result[] = array(
//           'text' => $category['name'], 'href' => $category['name'].'/'
//         );
//       }
//       $this->linkList = $result;
//       return;
//     }
    $queryList = Db::getAll(
      'SELECT name FROM query ORDER BY popularity_rank'
        .' LIMIT '.$offset.', 100'
    );
    foreach ($queryList as $query) {
      $result[] = array(
        'text' => $query['name'], 'href' => '/'.$query['name'].'/'
      );
    }
    $this->linkList = $result;
  }

  private function verifyPagination() {
    if (count($this->linkList) !== 0) {
      return;
    }
    if ($this->getAmount() !== 0 && $GLOBALS['PAGE'] !== 1) {
      $this->stop();
      header('HTTP/1.1 301 Moved Permanently');
      header('Location: .');
      return;
    }
    //throw new NotFoundException;
  }

  private function renderPagination() {
    PaginationScreen::render(
      $GLOBALS['PAGE'], $this->getAmount(), '', '', 100, 100, ''
    );
  }

  private function getAmount() {
    if ($this->category !== null) {
      return intval($this->category['query_amount']);
    }
    return 1000;
  }
}