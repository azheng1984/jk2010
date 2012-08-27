<?php
class AdministratorReportScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<div class="box">';
    echo '<div>分组 时间：日/月/年 | 时间区间（日/月/年 日历选择）</div>';
    echo '<div>时间 | 导入流量 | 导出流量 | 订单数量 | 订单支付金额 | 订单佣金 | 导入 CPC | 导出 CPC</div>';
    echo '</div>';
  }

  protected function getTitle() {
    return '管理员/效果报告';
  }
}