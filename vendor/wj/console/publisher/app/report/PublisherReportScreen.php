<?php
class PublisherReportScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<hr /><ul>';
    echo '<li>单位：日/月/年 | 日期区间（日/月/年 日历选择） | 导入类型（着陆页（商家列表/商品搜索/其它（包括 404 页面，关于，购物排行榜））/来源网站（只在用户有一个以上网站时显示）/自定义渠道）</li>';
    echo '<li>时间 | 流量 | 活跃订单佣金 | 收入</li>';
    echo '</ul>';
  }
}