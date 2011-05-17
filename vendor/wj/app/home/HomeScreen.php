<?php
class HomeScreen extends Screen {
  public function renderContent() {
    echo '<div>[<a href="?view=new_category">新建分类</a>]</div>';
    foreach (Category::getList() as $row) {
      echo '<div><a href="/'.urlencode($row['name']).'/">'.$row['name'].'</a> <input type="button" value="删除" /></div>';
    }
  }
}