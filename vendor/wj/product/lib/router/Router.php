<?php
class Router {
  private $path = '/category';

  public function execute() {
    $sections = explode('?', $_SERVER['REQUEST_URI'], 2);
    if ($sections[0] === '/') {
      return '/home';
    }
    foreach (explode('/', $sections[0]) as $section) {
      if ($section === '') {
        continue;
      }
      if ($this->path === '/category') {
        $this->parseCategoryPath($section);
        continue;
      }
      if ($this->path === '/product_list') {
        $this->parseProductPath($section);
        continue;
      }
      $this->parsePropertyPath($section);
    }
    return $this->path;
  }

  private function parseCategoryPath($section) {
    $category = Category::get(
      urldecode($section), $this->getContext('category', true)
    );
    $this->setContext('category', $category);
    if ($category->isLeaf()) {
      $this->path = '/product_list';
    }
  }

  private function parseProductPath($section) {
    $product = Product::get($section, $this->getContext('category'));
    $this->setContext('product', $product);
    $this->path = '/product';
  }

  private function parsePropertyPath($section) {
    $property = Property::get($section, $this->getContext('product'));
    $this->setContext('property', $property);
    $this->path = '/property';
  }

  private function setContext($key, $value) {
    if ($value === null) {
      throw new NotFoundException;
    }
    $GLOBALS[$key] = $value;
  }

  private function getContext($key, $allowNull = false) {
    $result = isset($GLOBALS[$key]) ? $GLOBALS[$key] : null;
    if ($result === null && !$allowNull) {
      throw new NotFoundException;
    }
    return $result;
  }
}