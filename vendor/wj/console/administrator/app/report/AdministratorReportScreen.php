<?php
class AdministratorReportScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<ul>';
    echo '<li>分组 时间：日/月/年 | 时间区间（日/月/年 日历选择）</li>';
    echo '<li>时间 | 导入流量 | 导出流量 | 订单数量 | 订单支付金额 | 活跃订单佣金 | 导入 CPC | 导出 CPC | 完成订单佣金</li>';
    echo '</ul>';
  }

  protected function getTitle() {
    return '管理员/效果报告';
  }
}