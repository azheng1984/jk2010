<?php
/*
/数码/ - category
/数码/手机/ - leaf category
/数码/手机/?page=2 - leaf category page 2
/数码/手机/?品牌=惠普 - leaf category + filter
/数码/手机/?view=edit - edit category
/数码/手机/?view=new_category - new category
/数码/手机/?view=new_product - new product
/数码/手机/?view=edit_property - edit property
/数码/手机/CPU频率?view=edit - edit property
/数码/手机/Nokia-2200C-手机/ - product
/数码/手机/Nokia-2200C-手机/图片/正面.jpg - image

 */
class Router {
  private $category;

  private function analyze($section) {
    $parentId = null;
    if ($this->category !== null) {
      $parentId = $this->category['id'];
    }
    $this->category = Category::get(urldecode($section), $parentId);
    if ($this->category !== null) {
      $_GLOBAL['category'][] = $this->category;
      return true;
    }
    return false;
  }

  public function execute() {
    $tmps = explode('?', $_SERVER['REQUEST_URI'], 2);
    $path = $tmps[0];
    if ($path  === '/search' || $path === '/') {
      return $path;
    }
    $sections = explode('/', $_SERVER['REQUEST_URI']);
    $_GLOBAL['category'] = array();
    foreach ($sections as $section) {
      $result = false;
      if ($section !== '' && $this->analyze($section) === false) {
        break;
      }
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