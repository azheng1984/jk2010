<?php
class ProductListScreen extends Screen {
  public function renderContent() {
    $breadcrumb = new Breadcrumb;
    $breadcrumb->render();
    echo '[<a href="?view=edit">编辑</a> | <a href="?view=new_product">新建产品</a>]';
    echo '<br />';
    print 'product list';
  }
}