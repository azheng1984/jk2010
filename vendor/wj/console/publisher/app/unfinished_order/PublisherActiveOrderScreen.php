<?php
class PublisherActiveOrderScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<div class="box"><br /> level 1(order) 时间 | 渠道 | 跟踪编号 | 商家 | 订单编号 | 交易金额 | 佣金<br />
level 2(order detail) 商品编号 | 商品名称 | 单价 | 数量 | 总价 | 佣金<br />';
    echo '总计：数量 交易金额 佣金</div>';
  }

  protected function getTitle() {
    return '广告发布商/活跃订单';
  }
}