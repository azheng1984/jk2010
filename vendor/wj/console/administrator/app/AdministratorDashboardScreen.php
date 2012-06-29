<?php
class AdministratorDashboardScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<hr />今日自然流量 今日广告发布商流量 今日导出流量 活跃订单佣金 未支付佣金';
    echo '<br />广告发布商总数 商家总数 商品总数';
  }
}