<?php
class PublisherDashboardScreen extends PublisherScreen {
  protected function renderMerchantContent() {
    echo '<hr />今日流量 活跃订单总量 活跃订单总金额 活跃订单佣金 未支付佣金<br />商品总数';
  }
}