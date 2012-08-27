<?php
class AdministratorHomeScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<br />【今日】<br />导入流量 | 导出流量 | 预计订单数量 | 预计订单支付金额 | 预计订单佣金 | 预计导入 CPC | 预计导出 CPC<br />';
    echo '<br />【总计】<br />活跃订单佣金 未付款 未收款<br />';
    echo '活跃广告发布商 活跃商家 商品 促销活动';
  }

  protected function getTitle() {
    return '管理员';
  }
}