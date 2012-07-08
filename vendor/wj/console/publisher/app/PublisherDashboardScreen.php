<?php
class PublisherDashboardScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<div class="box"><div class="title">今日</div>流量 | 新增活跃订单佣金 | 新增收入</div>';
    echo '<div class="box"><div class="title">总计</div>活跃订单佣金 | 未支付收入 | 正在付款（只在存在的时候显示）</div>';
  }
}