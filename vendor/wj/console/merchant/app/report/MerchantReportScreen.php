<?php
class MerchantReportScreen extends MerchantScreen {
  protected function renderMerchantContent() {
    echo '<hr /><ul>';
    echo '<li>单位：日/月/年 | 下单日期区间（日/月/年 日历选择） | 流量导出类型（首页/促销/商品）</li>';
    echo '<li>level 1(summary) 时间 | 流量 | [完成订单 | 活跃订单]总数 | [完成订单 | 活跃订单]金额 | [完成订单 | 活跃订单]佣金</li>';
    echo '<li>level 2(order) 订单编号 | 订单支付金额 | 状态 | 佣金</li>';
    echo '<li>level 3(order detail) 商品编号 | 商品名称 | 单价 | 数量 | 总价 | 佣金</li>';
    echo '</ul>';
  }
}