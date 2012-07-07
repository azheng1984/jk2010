<?php
class PublisherDashboardScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<br />【今日】<br />流量 | 新增活跃订单佣金 | 新增收入<br />';
    echo '<br />【总计】<br />活跃订单佣金 | 未支付收入 | 正在付款（只在存在的时候显示）<br />';
  }
}