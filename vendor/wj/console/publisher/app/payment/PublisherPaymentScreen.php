<?php
class PublisherPaymentScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<hr />【未付款 账户余额:￥xx】金额 商家<br />【已付款】时间 金额 商家';
  }
}