<?php
class MerchantActiveOrderScreen extends MerchantScreen {
  protected function renderMerchantContent() {
    echo '<hr /><ul>';
    echo '订单号：[][搜索]';
    echo '<li>单位：日/周/月/年 | 下单日期区间 | 导出类型（首页/促销/商品）</li>';
    echo '<li>level 1(summary) 单位总数 | 单位总价 | 单位总佣金</li>';
    echo '<li>level 2(order) 订单号 | 订单价格 | 状态 | 佣金</li>';
    echo '<li>level 3(order detail) 产品 ID | 产品名称 | 价格 | 佣金</li>';
    echo '</ul>';
  }
}