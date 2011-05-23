<?php
class HomeScreen extends Screen {
  public function renderContent() {
    foreach (Category::getList() as $row) {
      echo '<div><a href="/'.urlencode($row['name']).'/">'.$row['name'].'</a></div>';
    }
  }
}