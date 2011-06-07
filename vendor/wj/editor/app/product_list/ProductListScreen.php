<?php
class ProductListScreen extends Screen {
  public function renderContent() {
    $breadcrumb = new Breadcrumb;
    $breadcrumb->render();
    echo '[<a href="/category/edit?id='.$_GET['category_id'].'">编辑</a> | <a href="/product/new?category_id='.$_GET['category_id'].'">新建产品</a>]';
    echo '<br />';
    print 'product list';
  }
}