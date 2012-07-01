<?php
class AdministratorPaymentScreen extends AdministratorScreen {
  protected function renderAdministratorContent() {
    echo '<hr />【收款】';
    echo '<br />level 1(merchant) 商家 佣金 <input type="submit" value="已收款" /><br />';
    echo 'level 2(order) 订单编号 | 订单支付金额 | 佣金<br />';
    echo 'level 3(order detail) 商品编号 | 商品名称 | 购买价 | 数量 | 总价 | 佣金<br />';
    echo '<br />【付款】';
        echo '<br />level 1(merchant) 广告发布商 佣金 <input type="submit" value="已付款" /><br />';
    echo 'level 2(order) 订单编号 | 订单支付金额 | 佣金<br />';
    echo 'level 3(order detail) 商品编号 | 商品名称 | 购买价 | 数量 | 总价 | 佣金<br />';
  }
}