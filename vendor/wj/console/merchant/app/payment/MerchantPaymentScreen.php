<?php
class MerchantPaymentScreen extends MerchantScreen {
  protected function renderMerchantContent() {
    echo '<hr /><ul>';
    echo '<hr />【未付款】<br />账户余额: ￥xx <input type="submit" value="付款" />';
    echo '<br />level 1(merchant) 广告发布商 佣金<br />';
    echo 'level 2(order) 订单编号 | 订单支付金额 | 佣金<br />';
    echo 'level 3(order detail) 商品编号 | 商品名称 | 购买价 | 数量 | 总价 | 佣金<br />';
    echo '<br />【正在付款】<br />付款总额: ￥xx <input type="submit" value="取消" /><br />level 1(summary) 广告发布商 佣金<br />';
    echo 'level 2(order) 订单编号 | 订单支付金额 | 佣金<br />';
    echo 'level 3(order detail) 商品编号 | 商品名称 | 购买价 | 数量 | 总价 | 佣金<br />';
    echo '<br />【已付款】';
    echo '<br />level 0 (summary) 付款日期 | 付款金额';
    echo '<br />level 1(merchant) 广告发布商 佣金<br />';
    echo 'level 2(order) 订单编号 | 订单支付金额 | 佣金<br />';
    echo 'level 3(order detail) 商品编号 | 商品名称 | 购买价 | 数量 | 总价 | 佣金<br />';
    echo '</ul>';
  }
}