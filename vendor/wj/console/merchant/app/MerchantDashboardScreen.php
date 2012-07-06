<?php
class MerchantDashboardScreen extends MerchantScreen {
  protected function renderMerchantContent() {
    echo '<hr />【今日】<br />流量 活跃订单总量 活跃订单总金额 活跃订单佣金',
      '<br />【总计】<br />活跃订单总量 活跃订单总金额 活跃订单佣金<br />';
    echo '未支付佣金 正在支付佣金（只在存在的时候显示）<br /> 商品总数 促销活动总数';
  }
}