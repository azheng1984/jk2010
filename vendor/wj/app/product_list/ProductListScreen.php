<?php
class ProductListScreen extends Screen {
  public function renderContent() {
    echo '[<a href="edit">编辑</a>]';
    echo '<a href="'.urlencode('惠普P2200笔记本电脑').'/">惠普P2200笔记本电脑</a>';
  }
}