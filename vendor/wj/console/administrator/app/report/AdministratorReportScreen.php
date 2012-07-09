<?php
class AdministratorReportScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<hr /><ul>';
    echo '<li>单位：日/月/年 | 下单日期区间（日/月/年 日历选择） | 视角: 商家/广告发布商 | 商家/广告发布商: []（选定后包括 商家/广告发布商 的所有功能）</li>';
    echo '<li>level 1(summary) 时间 | [完成订单 | 活跃订单]总数 | [完成订单 | 活跃订单]金额 | [完成订单 | 活跃订单]佣金</li>';
    echo '<li>level 1.5 (merchant) 商家 | 流量 | [完成订单 | 活跃订单]数量 | [完成订单 | 活跃订单]金额 | [完成订单 | 活跃订单]佣金</li>';
    echo '<li>level 1.5 level 2 (publisher) 广告发布商 | 流量 | [完成订单 | 活跃订单]总数 | [完成订单 | 活跃订单]金额 | [完成订单 | 活跃订单]佣金</li>';
    echo '<li>level 2(order) 订单编号 | 订单支付金额 | 状态 | 佣金</li>';
    echo '<li>level 3(order detail) 商品编号 | 商品名称 | 单价 | 数量 | 总价 | 佣金</li>';
    echo '</ul>';
  }

  protected function getTitle() {
    return '管理员/效果报告';
  }
}