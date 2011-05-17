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
      $previousCategory = null;
      foreach ($sections as $section) {
        if ($section !== '') {
          if ($section === 'edit' || $section === 'new') {
            $type = '/'.$section;
            break;
          }
          $parentId = null;
          if ($previousCategory !== null) {
            $parentId = $previousCategory['id'];
          }
          $row = Category::get(urldecode($section), $parentId);
          $previousCategory = $row;
          if ($row !== false) {
            $_GET['category'][] = $row;
            if ($row['table_prefix'] !== null) {
              $isLeafCategory = true;
            }
          } elseif ($isLeafCategory) {
            $isProduct = true;
          } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
              if (isset($_POST['type']) && $_POST['type'] === 'category' || $_POST['type'] === 'product') {
                if (isset($_POST['_method'])) {
                  $_SERVER['REQUEST_METHOD'] = $_POST['_method'];
                }
                return '/'.$_POST['type'];
              }
            }
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
        } elseif ($type === '/product/new') {
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