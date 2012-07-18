<?php
class MerchantActiveOrderScreen extends MerchantScreen {
  protected function renderMerchantContent() {
    echo '<div class="box">level 1(order) 时间 | 编号 | 支付金额 | 佣金<br />
level 2(order detail) 商品编号 | 商品名称 | 单价 | 数量 | 总价 | 佣金</div>';
    echo '<div class="box">总计：订单数量 订单支付金额 活跃订单佣金</div>';
  }

  protected function getTitle() {
    return '商家/活跃订单';
  }
}