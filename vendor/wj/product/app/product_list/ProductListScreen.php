<?php
class ProductListScreen extends Screen {
  public function renderContent() {
    $breadcrumb = new Breadcrumb;
    $breadcrumb->render();
    echo '[<a href="'.$GLOBALS['category']->getEditLink().'">编辑</a> | <a href="'.$GLOBALS['category']->getNewProductLink().'">新建产品</a>]';
    echo '<br />';
    print 'product list';
  }
}