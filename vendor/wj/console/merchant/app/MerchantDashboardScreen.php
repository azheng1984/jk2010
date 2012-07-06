<?php
class MerchantDashboardScreen extends MerchantScreen {
  protected function renderMerchantContent() {
    echo '<hr />【今日】<br />流量<br /> level 1 新增订单 | 完成订单<br />';
    echo 'level 2 数量 金额 佣金<br />';
    echo '<br />【总计】<br />';
    echo '活跃订单佣金 未支付佣金 正在支付佣金（只在存在的时候显示）<br />商品总数 促销活动总数';
  }
}