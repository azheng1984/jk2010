<?php
class AdministratorReportScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<hr /><ul>';
    echo '订单号：<input type="text" /> <input type="submit" value="搜索" />';
    echo '<li>[收入 | 活跃订单] 单位：日/周/月/年 | 下单日期区间（日/月/周/年 日历选择）</li>';
    echo '<li>level 1(summary) 时间 | 单位订单总数 | 单位总订单金额 | 单位总佣金</li>';
    echo '<li>level 1.5 (merchant) 商家 | 单位订单总数 | 单位总订单金额 | 佣金</li>';
    echo '<li>level 1.5.1 (publisher) 广告发布商 | 单位订单总数 | 单位总订单金额 | 佣金</li>';
    echo '<li>level 2(order) 订单编号 | 订单支付金额 | 状态 | 佣金</li>';
    echo '<li>level 3(order detail) 商品编号 | 商品名称 | 购买价 | 数量 | 总价 | 佣金</li>';
    echo '</ul>';
  }
}