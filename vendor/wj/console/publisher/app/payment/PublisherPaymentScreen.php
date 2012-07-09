<?php
class PublisherPaymentScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<div class="box"><div class="title">未付款</div>账户余额: ￥xx <input type="submit" value="付款" /> 设置（自动付款）';
    echo '<br />level 1 商家 佣金</div>';
    echo '<div class="box"><div class="title">正在付款</div>付款总额: ￥xx <br />level 1 商家 佣金</div>';
    echo '<div class="box"><div class="title">已付款</div>';
    echo '<br />level 1 付款日期 | 付款金额';
    echo '<br />level 2 商家 | 佣金</div>';
  }

  protected function getTitle() {
    return '广告发布商/结算';
  }
}