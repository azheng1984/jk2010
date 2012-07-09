<?php
class AdministratorDashboardScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<hr />【今日】<br />导入流量 导出流量<br /> level 1 新增订单 | 完成订单<br />';
    echo 'level 2 数量 金额 佣金<br />';
    echo '<br />【总计】<br />活跃订单佣金 未付款 未收款<br />';
    echo '活跃广告发布商 活跃商家 商品 促销活动';
  }

  protected function getTitle() {
    return '商家';
  }
}