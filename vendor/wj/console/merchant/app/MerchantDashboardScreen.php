<?php
class MerchantDashboardScreen extends MerchantScreen {
  protected function renderMerchantContent() {
    echo '<hr />今日】<br />今日流量 | 活跃订单总量 活跃订单总金额 活跃订单佣金 | 未支付佣金 正在支付佣金（只在存在的时候显示）',
      '【总计】<br /><br />商品总数 | 促销活动总数';
  }
}