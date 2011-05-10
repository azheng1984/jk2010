<?php
class HomeScreen extends Screen {
  public function renderContent() {
    echo '<div>200,000,000条商品信息 | 10,000个活跃商家</div>';
    echo '<div>[<a href="new">新建分类</a>]</div>';
    foreach (Category::getList() as $row) {
      echo '<a href="/'.urlencode($row['name']).'/">'.$row['name'].'</a> <input type="button" value="删除" />';
    }
  }
}