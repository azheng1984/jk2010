<?php
class AdministratorDashboardScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<hr />【今日】<br />导入流量 导出流量 活跃订单总量 活跃订单总金额 活跃订单佣金 收入<br />';
    echo '【总计】<br />活跃订单总量 活跃订单总金额 活跃订单总佣金<br />';
    echo '未支付佣金 正在支付佣金（只在存在的时候显示）';
    echo '<br />活跃广告发布商总数 活跃商家总数 商品总数 促销活动总数';
  }
}