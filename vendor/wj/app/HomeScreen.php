<?php
class HomeScreen extends Screen {
  public function renderContent() {
    echo '<div>200,000,000条商品信息 | 10,000个活跃商家</div>';
    foreach (Db::execute('select * from global_category where parent_id is null') as $row) {
      echo '<a href="/'.urlencode($row['name']).'/">'.$row['name'].'</a> ';
    }
  }
}