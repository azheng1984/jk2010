<?php
class MerchantDashboardScreen extends MerchantScreen {
  protected function renderMerchantContent() {
    echo '<br />【今日】<br />流量 订单数量 订单支付金额 活跃订单佣金<br />';
    echo '<br />【总计】<br />';
    echo '未支付佣金 | 正在支付佣金（只在存在的时候显示） | 未完成订单佣金<br />商品总数 | 促销活动总数';
  }

  protected function getTitle() {
    return '商家';
  }
}