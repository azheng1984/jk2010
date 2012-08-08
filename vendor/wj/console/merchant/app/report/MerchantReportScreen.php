<?php
class MerchantReportScreen extends MerchantScreen {
  protected function renderMerchantContent() {
    echo '<ul>';
    echo '<li>时间区间（日/月/年 日历选择） <br />分组: [时间：日/月/年] <br /> </li>';
    echo '<li>时间 | 流量 | 订单数量 | 订单支付金额 | 活跃订单佣金 | 完成订单佣金 | CPC</li>';
    echo '</ul>';
  }

  protected function getTitle() {
    return '商家/效果报告';
  }
}