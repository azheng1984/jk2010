<?php
class PublisherDashboardScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<hr />今日流量 活跃订单佣金 未支付佣金 正在支付佣金（只在存在的时候显示）<br />';
  }
}