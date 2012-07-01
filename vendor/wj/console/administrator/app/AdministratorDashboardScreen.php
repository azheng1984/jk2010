<?php
class AdministratorDashboardScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<hr />（自然流量 & 广告发布商 & 总计）今日导入流量 今日导出流量 活跃订单佣金 未支付佣金';
    echo '<br />广告发布商总数 商家总数 商品总数 促销活动总数';
  }
}