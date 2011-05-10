<?php
class ProductListScreen extends Screen {
  public function renderContent() {
    $breadcrumb = new Breadcrumb;
    $breadcrumb->render();
    echo '[<a href="edit">编辑</a> | <a href="new">新建产品</a>]';
    echo '<br />';
    print 'product list';
  }
}