<?php
class PublisherPaymentScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<hr />【未付款】<br />账户余额: ￥xx <input type="submit" value="付款" /> 设置（自动付款）<br />金额 商家',
      '<br />【正在付款】<br />【已付款】<br />时间 金额 商家';
  }
}