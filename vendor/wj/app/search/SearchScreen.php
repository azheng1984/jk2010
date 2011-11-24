<?php
class SearchScreen extends Screen {
  private $category = false;
  private $properties = null;

  public function __construct() {
    if (isset($GLOBALS['URI']['CATEGORY_NAME'])) {
      $this->category = DbCategory::getByName($GLOBALS['URI']['CATEGORY_NAME']);
    }
    if (isset($GLOBALS['URI']['PROPERTIES'])) {
      $this->parseProperties();
    }
  }

  private function parseProperties() {
    $this->properties = array();
    $key = null;
    $values = null;
    $items = explode('&', $GLOBALS['URI']['PROPERTIES']);
    foreach ($items as $item) {
      $tmps = explode('=', $item, 2);
      if (count($tmps) === 2) {
        if ($key !== null) {
          $this->properties[] = array('key' => $key, 'values' => $values);
        }
        $key = DbProperty::getKeyByName(
          $this->category['id'], array_shift($tmps)
        );
        $values= array();
      }
      $values[] = DbProperty::getValueByName($key, $tmps[0]);
    }
  }

  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
    $this->renderCssLink('search');
  }

  protected function renderBodyContent() {
    $this->renderBreadcrumb();
  }

  private function renderBreadcrumb() {
    $query = htmlentities($GLOBALS['URI']['QUERY'], ENT_QUOTES, 'utf-8');
    echo '<div id="h1_wrapper"><h1>';
    if ($this->category !== false) {
      echo '<a href="..">', $query, '</a> &rsaquo; '.$this->category['name'];
    } else {
      echo $query;
    }
    echo '</h1></div>';
  }
}