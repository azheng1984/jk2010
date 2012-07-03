<?php
class PublisherReportScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<hr /><ul>';
    echo '[收入 | 活跃订单] 订单号：<input type="text" /> <input type="submit" value="搜索" />';
    echo '<li>单位：日/周/月/年 | 下单日期区间（日/月/周/年 日历选择） | 导入类型（着陆页（商家列表/商品搜索/其它（包括 404 页面））/来源网站（只在用户有一个以上网站时显示）/自定义渠道）</li>';
    echo '<li>level 1(summary) 时间 | 单位订单总数 | 单位总订单金额 | 单位总佣金</li>';
    echo '<li>level 2(order) 订单编号 | 订单支付金额 | 状态 | 佣金</li>';
    echo '<li>level 3(order detail) 商品编号 | 商品名称 | 购买价 | 数量 | 总价 | 佣金</li>';
    echo '</ul>';
  }
}