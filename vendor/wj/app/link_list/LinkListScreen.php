<?php
class LinkListScreen extends Screen {
  private $alphabetIndex = null;
  private $page = 1;
  private $amount;
  private $linkList = array();

  public function __construct() {
    if (isset($GLOBALS['URI']['ALPHABET_INDEX'])) {
      $this->alphabetIndex = ord($GLOBALS['URI']['ALPHABET_INDEX']);
    }
    if (isset($GLOBALS['URI']['PAGE'])) {
      $this->page = $GLOBALS['URI']['PAGE'];
    }
    if ($GLOBALS['URI']['LIST_TYPE'] === 'category') {
      $categoryList = DbCategory::getList($this->alphabetIndex, $this->page);
      foreach ($categoryList as $category) {
        $this->linkList[] = array(
          'text' => $category['name'], 'href' => $category['name'].'/'
        );
      }
      $this->amount = DbCategory::count($this->alphabetIndex);
      return;
    }
    if ($GLOBALS['URI']['LIST_TYPE'] === 'key') {
      $category = $GLOBALS['URI']['CATEGORY'];
      $keyList = DbPropertyKey::getList(
        $category['id'], $this->alphabetIndex, $this->page
      );
      foreach ($keyList as $key) {
        $this->linkList[] = array(
          'text' => $key['name'], 'href' => $key['name'].'/'
        );
      }
      $this->amount = DbPropertyKey::count($this->alphabetIndex);
      return;
    }
    if ($GLOBALS['URI']['LIST_TYPE'] === 'value') {
      $key = $GLOBALS['URI']['KEY'];
      $valueList = DbPropertyKey::getList(
        $key['id'], $this->alphabetIndex, $this->page
      );
      foreach ($valueList as $value) {
        $this->linkList[] = array(
          'text' => $value['name'], 'href' => $value['name'].'/'
        );
      }
      $this->amount = DbPropertyValue::count($this->alphabetIndex);
      return;
    }
    if ($GLOBALS['URI']['LIST_TYPE'] === 'query') {
      $this->initializeQueryList();
    }
  }

  private function initializeQueryList() {
    if (isset($GLOBALS['URI']['VALUE'])) {
      $result = QuerySearch::searchByPropertyValue();
      return;
    }
    DbQuery::getList();
  }

  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
    $this->renderCssLink('index_breadcrumb');
    $this->renderCssLink('index');
  }

  protected function renderBodyContent() {
    $this->renderBreadcrumb();
    $this->renderIndex();
  }

  private function renderIndex() {
    echo '<div id="index">';
    $this->renderAlphabetList();
    $this->renderTable();
    $this->renderPagination();
    echo '</div>';
  }

  private function renderBreadcrumb() {
    echo '<div id="breadcrumb">',
      '<span class="home"><a href="/"><img alt="首页" src="/img/home.png" /></a></span><h1>分类</h1>',
      '</div>';
  }

  private function renderAlphabetList() {
    $char = 64;
    echo '<div id="alphabet">索引: ';
    for ($i = 0; $i < 24; ++$i) {
      ++$char;
      $index = chr($char);
      if ($char === $this->alphabetIndex) {
       echo ' <span>', $index, '</span>';
       continue;
      }
      echo ' <a href="', $index, '">', $index, '</a>';
    }
    if ($this->alphabetIndex === 48) {
      echo ' <span>0_9</span>';
    } else {
      echo ' <a href="0_9">0_9</a>';
    }
    echo '</div>';
  }

  private function renderTable() {
    $amount = count($this->linkList);
    $index = 0;
    echo '<table>';
    for ($i = 0; $i < 12; ++$i) {
      echo '<tr>';
      for ($j = 0; $j < 5; ++$j) {
        echo '<td>';
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
    $prefix = '';
    if ($this->alphabetIndex !== null) {
      $prefix = $this->alphabetIndex.'-';
    }
    PaginationScreen::render($this->amount, 60, $prefix, '');
  }
}