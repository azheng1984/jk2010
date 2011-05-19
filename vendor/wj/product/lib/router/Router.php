<?php
class Router {
  private $path = 'category';

  public function execute() {
    $sections = explode('?', $_SERVER['REQUEST_URI'], 2);
    if ($sections[0] === '/') {
      return '/home';
    }
    $GLOBALS['target_data'] = array();
    foreach (explode('/', $sections[0]) as $section) {
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
    $parent = null;
    if (isset($GLOBALS['target_data']['category'])) {
      $parent = $GLOBALS['target_data']['category'];
    }
    $category = Category::getData($section, $parent);
    $this->setTarget('category', $category);
    if ($category['table_prefix'] !== null) {
      $this->path = '/product_list';
    }
  }

  private function parseProductPath($section) {
    $product = Product::getData($section, $GLOBALS['target']['category']);
    $this->setTarget('product', $product);
    $this->path = '/product';
  }

  private function parsePropertyPath($section) {
    $property = Property::getData($section, $GLOBALS['target']['product']);
    $this->setTarget('property', $property);
    $this->path = 'property';
  }

  private function setTarget($key, $value) {
    if ($value === null) {
      throw new NotFoundException;
    }
    $GLOBALS['target_data'][$key] = $value;
  }
}