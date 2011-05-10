<?php
class Router {
  public function execute() {
    $tmps = explode('?', $_SERVER['REQUEST_URI']);
    if ($tmps[0]  === '/search') {
      return '/search';
    }
    if ($_SERVER['REQUEST_URI'] !== '/') {
      $sections = explode('/', $_SERVER['REQUEST_URI']);
      $isLeafCategory = false;
      $_GET['category'] = array();
      $type = null;
      $isProduct = false;
      foreach ($sections as $section) {
        if ($section !== '') {
          if ($section === 'edit' || $section === 'new') {
            $type = '/'.$section;
            break;
          }
          $row = Category::get(urldecode($section));
          if ($row !== false) {
            $_GET['category'][] = $row;
            if ($row['table_prefix'] !== null) {
              $isLeafCategory = true;
            }
          } elseif ($isLeafCategory) {
            $isProduct = true;
          } else {
            throw new NotFoundException;
          }
        }
      }
      if (!isset($_GET['category'])) {
        throw new NotFoundException;
      }
      if ($isProduct) {
        return '/product'.$type;
      }
      if ($isLeafCategory) {
        if ($type === null) {
          return '/product_list';
        } elseif ($type === '/new') {
          return '/product/new';
        } elseif ($type === '/edit') {
          return '/category/edit';
        } else {
          throw new NotFoundException;
        }
      }
      return '/category'.$type;
    }
    return $_SERVER['REQUEST_URI'];
  }
}