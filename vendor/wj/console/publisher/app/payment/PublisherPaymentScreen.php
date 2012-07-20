<?php
class PublisherPaymentScreen extends PublisherScreen {
  protected function renderPublisherContent() {
    echo '<div class="box"><div class="title">未付款</div>账户余额: ￥xx <input type="submit" value="付款" /> 设置（自动付款，代扣税费（在付款时的选项会被自动保存，避免重复操作））';
    echo '<br />level 1 商家 佣金</div>';
    echo '<div class="box"><div class="title">正在付款</div>付款总额: ￥xx <br />level 1 商家 佣金<br />
level 2.1(order) 时间 | 渠道 | 跟踪编号 | 商家 | 订单编号 | 支付金额 | 佣金<br />
level 2.2(order detail) 商品编号 | 名称 | 单价 | 数量 | 总价 | 佣金
<br />总计：数量 支付金额 佣金</div>';
    echo '<div class="box"><div class="title">已付款</div>';
    echo '<br />level 1 付款日期 | 付款金额';
    echo '<br />level 2 商家 | 佣金<br />历史记录</div>';
  }

  protected function getTitle() {
    return '广告发布商/结算';
  }
}