<?php
class Router {
  public function execute() {
    if ($_SERVER['REQUEST_URI'] !== '/') {
      $isLeafCategory = false;
      $sections = explode('/', $_SERVER['REQUEST_URI']);
      $_GET['category'] = array();
      foreach ($sections as $section) {
        if ($section !== '') {
          if ($section === 'edit') {
            break;
          }
          $row = Category::get(urldecode($section));
          if ($row !== false) {
            $_GET['category'][] = $row;
            if ($row['table_prefix'] !== null) {
              $isLeafCategory = true;
            }
          } elseif ($isLeafCategory) {
            return '/product';
          } else {
            throw new NotFoundException;
          }
        }
      }
      if (!isset($_GET['category'])) {
        throw new NotFoundException;
      }
      if ($isLeafCategory) {
        return '/product_list';
      }
      return '/category';
    }
    return $_SERVER['REQUEST_URI'];
  }
}