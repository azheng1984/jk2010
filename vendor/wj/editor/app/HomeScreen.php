<?php
class HomeScreen extends Screen {
  public function renderContent() {
    echo '+ <a href="http://editor.wj.com/category/new">分类</a>';
    foreach (Category::getList() as $row) {
      echo '<div><a href="/category?id='.$row['id'].'">'.$row['name'].'</a></div>';
    }
  }
}