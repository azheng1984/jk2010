<?php
class PublisherPaymentScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '【未付款】<br />账户余额: ￥xx <input type="submit" value="付款" /> 设置（自动付款）';
    echo '<br />level 1 商家 佣金<br />';
    echo '<br />【正在付款】<br />付款总额: ￥xx <br />level 1 商家 佣金<br />';
    echo '<br />【已付款】';
    echo '<br />level 1 付款日期 | 付款金额';
    echo '<br />level 2 商家 | 佣金<br />';
  }
}