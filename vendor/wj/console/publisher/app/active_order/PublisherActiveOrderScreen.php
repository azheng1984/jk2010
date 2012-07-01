<?php
class PublisherActiveOrderScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<hr /><ul>';
    echo '订单号：[][搜索]';
    echo '<li>单位：日/周/月/年 | 下单日期区间 | 导入类型（网站/自定义渠道）</li>';
    echo '<li>level 1(summary) 单位总数 | 单位总价 | 单位总佣金</li>';
    echo '<li>level 2(order) 订单号 | 订单价格 | 状态 | 佣金</li>';
    echo '<li>level 3(order detail) 产品 ID | 产品名称 | 价格 | 佣金</li>';
    echo '</ul>';
  }
}