<?php
class Router {
  private $sections;
  private $length;

  public function __construct() {
    $this->sections = explode('/', $_SERVER['REQUEST_URI']);
    $this->length = count($this->sections);
  }

  public function getPath() {
    if ($this->length === 2) {
      if ($this->sections[1] !== '') {
        throw new NotFoundException;
      }
      $_GET['category'] = 'home';
      return 'category';
    }
    $_GET['category'] = $this->sections[1];
    if ($this->sections[$this->length - 1] === '') {
      return $this->getListPath();
    }
    return $this->getItemPath();
  }

  private function getListPath() {
    if ($this->length > 4) {
      throw new NotFoundException;
    }
    $path = 'category';
    if ($this->length === 4) {
      $this->parseCategoryList();
      $path = 'document_list';
    }
    return $path;
  }

  private function getItemPath() {
    if ($this->length !== 4) {
      throw new NotFoundException;
    }
    $this->parseCategoryList();
    $items = explode('.', $this->sections[3]);
    if (count($items) !== 2) {
      throw new NotFoundException;
    }
    if ($items[1] === 'html') {
       $this->map($items[0], array('id', 'url_name'));
      return 'document';
    }
    $this->map($items[0], array('image_database_index', 'id', 'url_name'));
    $_GET['type'] = $items[1];
    return 'image';
  }

  private function parseCategoryList() {
    $this->map($this->sections[2], array('database_index', 'page'));
  }

  private function map($string, $keys) {
    $items = explode('-', $string);
    if (count($items) !== count($keys)) {
      throw new NotFoundException;
    }
    $index = 0;
    foreach ($keys as $key) {
      $_GET[$key] = $items[$index++];
    }
  }
}