<?php
class Router {
  private $sections;
  private $length;

  public function __construct() {
    $this->sections = explode('/', $_SERVER['REQUEST_URI']);
    $this->length = count($this->sections);
  }

  public function getPath() {
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
    if ($this->length === 2) {
      $_GET['category'] = 'home';
      return $path;
    }
    if ($this->length > 2) {
      $_GET['current_category'] = $this->sections[1];
    }
    if ($this->length === 4) {
      $_GET['page'] = $this->sections[2];
      $path = 'document_list';
    }
    return $path;
  }

  private function getItemPath() {
    if ($this->length != 4) {
      throw new NotFoundException;
    }
    $items = explode('.', $this->section[3]);
    if (count($items) != 2) {
      throw new NotFoundException;
    }
    $_GET['url_name'] = $items[0];
    if ($items[1] === 'html') {
      return 'document';
    }
    $_GET['image_type'] = $items[1];
    return 'image';
  }
}