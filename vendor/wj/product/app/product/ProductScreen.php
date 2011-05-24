<?php
class ProductScreen extends Screen{
  public function renderContent() {
    $breadcrumb = new Breadcrumb;
    $breadcrumb->render();
    echo '<h1>'.$GLOBALS['product']->getTitle().'</h1>';
    echo $GLOBALS['product']->getContent();
  }
}